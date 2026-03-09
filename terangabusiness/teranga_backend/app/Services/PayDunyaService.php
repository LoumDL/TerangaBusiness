<?php

namespace App\Services;

use App\Interfaces\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayDunyaService implements PaymentGateway
{
    private string $baseUrl;
    private array  $headers;

    public function __construct()
    {
        $sandbox = config('paydunya.sandbox');

        $this->baseUrl = $sandbox
            ? 'https://app.paydunya.com/sandbox-api/v1'
            : 'https://app.paydunya.com/api/v1';

        $this->headers = [
            'PAYDUNYA-MASTER-KEY'  => config('paydunya.master_key'),
            'PAYDUNYA-PRIVATE-KEY' => config('paydunya.private_key'),
            'PAYDUNYA-TOKEN'       => config('paydunya.token'),
            'PAYDUNYA-PUBLIC-KEY'  => config('paydunya.public_key'),
            'Content-Type'         => 'application/json',
        ];
    }

    // Map des channels frontend → codes PayDunya
    private const CHANNEL_MAP = [
        'wave'   => 'wave_senegal',
        'orange' => 'orange_money_senegal_merci',
        'card'   => 'card',
    ];

    private function httpClient(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::withHeaders($this->headers)->timeout(30);
        // Sur Windows (dev), les certificats SSL locaux peuvent manquer
        if (config('paydunya.sandbox')) {
            $client = $client->withoutVerifying();
        }
        return $client;
    }

    public function initiate(array $payload): array
    {
        $response = $this->httpClient()
            ->post("{$this->baseUrl}/checkout-invoice/create", [
                'invoice' => [
                    'total_amount' => (int) $payload['montant'],
                    'description'  => $payload['description'],
                    'currency'     => 'XOF',
                ],
                'store' => [
                    'name' => config('app.name'),
                ],
                'custom_data' => [
                    'paiement_id' => $payload['paiement_id'],
                ],
                'actions' => [
                    'callback_url' => config('paydunya.callback_url'),
                    'return_url'   => config('paydunya.return_url') . '?paiement_id=' . $payload['paiement_id'],
                    'cancel_url'   => config('paydunya.cancel_url'),
                ],
            ]);

        if (! $response->successful()) {
            Log::error('PayDunya initiate HTTP error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('PayDunya indisponible. Veuillez réessayer.');
        }

        $data = $response->json();

        Log::info('PayDunya initiate response', $data);

        if (($data['response_code'] ?? '') !== '00') {
            Log::error('PayDunya initiate error', $data);
            throw new \RuntimeException($data['response_text'] ?? 'Erreur PayDunya.');
        }

        // PayDunya retourne l'URL dans response_text quand response_code = '00'
        $invoiceUrl = $data['invoice_url']
            ?? $data['checkout_url']
            ?? $data['hosted_url']
            ?? (filter_var($data['response_text'] ?? '', FILTER_VALIDATE_URL) ? $data['response_text'] : null)
            ?? null;

        if (! $invoiceUrl) {
            Log::error('PayDunya: invoice_url manquante', $data);
            throw new \RuntimeException('Impossible de récupérer l\'URL de paiement PayDunya.');
        }

        // Ajouter le channel préféré si spécifié
        if (! empty($payload['channel']) && isset(self::CHANNEL_MAP[$payload['channel']])) {
            $invoiceUrl .= '?channel=' . self::CHANNEL_MAP[$payload['channel']];
        }

        return [
            'token'        => $data['token'],
            'checkout_url' => $invoiceUrl,
        ];
    }

    public function directCharge(string $token, string $phone, string $provider): array
    {
        // PayDunya soft pay : envoie un push USSD au téléphone
        $response = $this->httpClient()
            ->post("{$this->baseUrl}/softpay/{$provider}", [
                'account_alias' => $phone,
                'token'         => $token,
            ]);

        Log::info('PayDunya directCharge response', [
            'provider' => $provider,
            'phone'    => $phone,
            'status'   => $response->status(),
            'body'     => $response->json(),
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Erreur lors de l\'envoi du push mobile.');
        }

        $data = $response->json();

        if (($data['response_code'] ?? '') !== '00') {
            throw new \RuntimeException($data['response_text'] ?? 'Paiement direct refusé.');
        }

        return [
            'statut'  => 'EN_ATTENTE',
            'message' => 'Validez le paiement sur votre téléphone.',
        ];
    }

    public function verify(string $token): array
    {
        $response = $this->httpClient()
            ->timeout(15)
            ->get("{$this->baseUrl}/checkout-invoice/details/{$token}");

        if (! $response->successful()) {
            Log::error('PayDunya verify HTTP error', [
                'token'  => $token,
                'status' => $response->status(),
            ]);
            throw new \RuntimeException('Impossible de vérifier le paiement.');
        }

        $data   = $response->json();
        $status = $data['status'] ?? 'pending';

        return match ($status) {
            'completed' => [
                'statut'  => 'VALIDÉ',
                'ref'     => $data['receipt_url'] ?? $token,
                'message' => 'Paiement validé par PayDunya.',
            ],
            'cancelled', 'failed' => [
                'statut'  => 'REJETÉ',
                'ref'     => $token,
                'message' => 'Paiement refusé ou annulé.',
            ],
            default => [
                'statut'  => 'EN_ATTENTE',
                'ref'     => $token,
                'message' => 'Paiement en cours de traitement.',
            ],
        };
    }
}
