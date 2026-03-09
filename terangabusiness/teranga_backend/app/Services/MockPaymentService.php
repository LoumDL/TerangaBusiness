<?php

namespace App\Services;

use App\Interfaces\PaymentGateway;

class MockPaymentService implements PaymentGateway
{
    public function initiate(array $payload): array
    {
        $token = 'MOCK-' . strtoupper(substr(md5(uniqid()), 0, 12));

        return [
            'token'        => $token,
            'checkout_url' => url("/api/v1/paiements/mock-checkout/{$token}"),
        ];
    }

    public function directCharge(string $token, string $phone, string $provider): array
    {
        usleep(500000); // 500ms simulé
        return [
            'statut'  => 'EN_ATTENTE',
            'message' => 'Push USSD simulé envoyé au ' . $phone . '. Validez sur votre téléphone.',
        ];
    }

    public function verify(string $token): array
    {
        usleep(300000); // 300ms simulé
        $success = rand(1, 10) <= 7; // 70% succès

        return [
            'statut'  => $success ? 'VALIDÉ' : 'REJETÉ',
            'ref'     => $token,
            'message' => $success ? 'Paiement simulé approuvé.' : 'Paiement simulé refusé.',
        ];
    }
}
