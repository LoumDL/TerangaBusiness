<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaiementRequest;
use App\Interfaces\PaymentGateway;
use App\Models\Historique;
use App\Models\Justificatif;
use App\Models\Paiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    public function __construct(private readonly PaymentGateway $paymentService)
    {
    }

    /**
     * Initie un paiement → retourne l'URL de checkout PayDunya.
     */
    public function store(PaiementRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $paiement = Paiement::create([
                'user_id'     => $request->user()->id,
                'description' => $request->description,
                'montant'     => $request->montant,
                'statut'      => 'EN_ATTENTE',
            ]);

            // Justificatif optionnel (utile pour paiements hors ligne)
            if ($request->hasFile('justificatif')) {
                $file       = $request->file('justificatif');
                $uuid       = Str::uuid()->toString();
                $extension  = $file->getClientOriginalExtension();
                $storedPath = $file->storeAs('justificatifs', $uuid . '.' . $extension, 'public');

                Justificatif::create([
                    'paiement_id'   => $paiement->id,
                    'file_url'      => '/storage/' . $storedPath,
                    'file_type'     => $file->getMimeType(),
                    'original_name' => $file->getClientOriginalName(),
                    'uploaded_at'   => now(),
                ]);
            }

            $user   = $request->user();
            $result = $this->paymentService->initiate([
                'paiement_id' => $paiement->id,
                'montant'     => $paiement->montant,
                'description' => $paiement->description,
                'user_email'  => $user->email,
                'user_name'   => $user->name,
                'channel'     => $request->channel,
            ]);

            $paiement->update([
                'paydunya_token' => $result['token'],
                'checkout_url'   => $result['checkout_url'],
            ]);

            DB::commit();

            return response()->json([
                'paiement_id'  => $paiement->id,
                'checkout_url' => $result['checkout_url'],
                'message'      => 'Paiement initié. Veuillez compléter le paiement.',
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur initiation paiement', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => $e->getMessage() ?: 'Erreur lors de l\'initialisation du paiement.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Webhook PayDunya — appelé automatiquement après paiement.
     * Route publique (pas de auth:sanctum).
     */
    public function webhook(Request $request): JsonResponse
    {
        // Vérification IPN hash (SHA-512 du secret + token)
        $token     = $request->input('data.invoice.token') ?? $request->input('token');
        $hash      = $request->header('X-PayDunya-Signature') ?? $request->input('hash', '');
        $ipnSecret = config('paydunya.ipn_secret');

        if ($ipnSecret && hash('sha512', $ipnSecret . $token) !== $hash) {
            Log::warning('PayDunya webhook: signature invalide', ['token' => $token]);
            return response()->json(['message' => 'Signature invalide.'], 401);
        }

        $paiement = Paiement::where('paydunya_token', $token)->first();

        if (! $paiement) {
            Log::warning('PayDunya webhook: paiement introuvable', ['token' => $token]);
            return response()->json(['message' => 'Paiement introuvable.'], 404);
        }

        // Déjà traité
        if ($paiement->statut !== 'EN_ATTENTE') {
            return response()->json(['message' => 'Déjà traité.']);
        }

        try {
            $result = $this->paymentService->verify($token);

            $paiement->update(['statut' => $result['statut']]);

            Historique::create([
                'user_id'     => $paiement->user_id,
                'type'        => 'PAIEMENT',
                'description' => $paiement->description,
                'montant'     => $paiement->montant,
                'statut'      => $result['statut'],
                'date'        => now(),
            ]);

            return response()->json(['message' => 'Webhook traité.']);

        } catch (\Throwable $e) {
            Log::error('Erreur webhook PayDunya', ['token' => $token, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Erreur traitement webhook.'], 500);
        }
    }

    /**
     * Polling du statut d'un paiement depuis le frontend.
     */
    public function status(Request $request, int $id): JsonResponse
    {
        $paiement = Paiement::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'statut'       => $paiement->statut,
            'checkout_url' => $paiement->checkout_url,
        ]);
    }

    /**
     * Endpoint mock — simule le retour PayDunya en développement.
     */
    public function mockCheckout(string $token): JsonResponse
    {
        if (app()->isProduction()) {
            abort(404);
        }

        $paiement = Paiement::where('paydunya_token', $token)->firstOrFail();

        if ($paiement->statut !== 'EN_ATTENTE') {
            return response()->json(['message' => 'Déjà traité.']);
        }

        $result = $this->paymentService->verify($token);

        $paiement->update(['statut' => $result['statut']]);

        Historique::create([
            'user_id'     => $paiement->user_id,
            'type'        => 'PAIEMENT',
            'description' => $paiement->description,
            'montant'     => $paiement->montant,
            'statut'      => $result['statut'],
            'date'        => now(),
        ]);

        $frontendUrl = config('paydunya.return_url') . '?paiement_id=' . $paiement->id . '&statut=' . $result['statut'];

        return response()->json([
            'statut'      => $result['statut'],
            'message'     => $result['message'],
            'redirect_to' => $frontendUrl,
        ]);
    }

    /**
     * Charge directe mobile money (Wave / Orange) — sans redirection PayDunya.
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'paiement_id' => ['required', 'integer'],
            'phone'       => ['required', 'string', 'regex:/^[0-9]{9}$/'],
        ], [
            'phone.regex' => 'Le numéro doit contenir 9 chiffres (ex: 771234567).',
        ]);

        $paiement = Paiement::where('id', $request->paiement_id)
            ->where('user_id', $request->user()->id)
            ->where('statut', 'EN_ATTENTE')
            ->firstOrFail();

        $providerMap = [
            'wave'   => 'wave-senegal',
            'orange' => 'orange-money-senegal-merci',
        ];

        // Déduire le provider depuis le token ou le channel stocké
        $channel  = $request->input('channel', 'wave');
        $provider = $providerMap[$channel] ?? 'wave-senegal';

        try {
            $result = $this->paymentService->directCharge(
                $paiement->paydunya_token,
                $request->phone,
                $provider,
            );

            return response()->json([
                'message'     => $result['message'],
                'paiement_id' => $paiement->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erreur direct charge', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => $e->getMessage() ?: 'Erreur lors de l\'envoi du push mobile.',
            ], 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $paiement = Paiement::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('justificatif')
            ->firstOrFail();

        return response()->json(['paiement' => $paiement]);
    }
}
