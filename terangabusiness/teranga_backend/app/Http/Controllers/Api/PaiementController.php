<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaiementRequest;
use App\Models\Historique;
use App\Models\Justificatif;
use App\Models\Paiement;
use App\Services\MockPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    public function __construct(private readonly MockPaymentService $paymentService)
    {
    }

    public function store(PaiementRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // 1. Créer le paiement en EN_ATTENTE
            $paiement = Paiement::create([
                'user_id'     => $request->user()->id,
                'description' => $request->description,
                'montant'     => $request->montant,
                'statut'      => 'EN_ATTENTE',
            ]);

            // 2. Stocker le justificatif
            $file           = $request->file('justificatif');
            $uuid           = Str::uuid()->toString();
            $extension      = $file->getClientOriginalExtension();
            $storedPath     = $file->storeAs('justificatifs', $uuid . '.' . $extension, 'public');

            Justificatif::create([
                'paiement_id'   => $paiement->id,
                'file_url'      => '/storage/' . $storedPath,
                'file_type'     => $file->getMimeType(),
                'original_name' => $file->getClientOriginalName(),
                'uploaded_at'   => now(),
            ]);

            // 3. Valider via MockPaymentService
            $result = $this->paymentService->validate([
                'paiement_id' => $paiement->id,
                'montant'     => $paiement->montant,
                'description' => $paiement->description,
            ]);

            // 4. Mettre à jour le statut
            $paiement->update(['statut' => $result['statut']]);

            // 5. Enregistrer dans l'historique
            Historique::create([
                'user_id'     => $request->user()->id,
                'type'        => 'PAIEMENT',
                'description' => $paiement->description,
                'montant'     => $paiement->montant,
                'statut'      => $result['statut'],
                'date'        => now(),
            ]);

            DB::commit();

            $paiement->load('justificatif');

            return response()->json([
                'paiement' => [
                    'id'               => $paiement->id,
                    'description'      => $paiement->description,
                    'montant'          => (float) $paiement->montant,
                    'statut'           => $paiement->statut,
                    'justificatif_url' => $paiement->justificatif?->file_url,
                    'created_at'       => $paiement->created_at,
                ],
                'message'  => $result['message'],
                'ref'      => $result['ref'],
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Une erreur est survenue lors du traitement du paiement.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
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
