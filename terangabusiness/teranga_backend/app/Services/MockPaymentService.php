<?php

namespace App\Services;

use App\Interfaces\PaymentGateway;

class MockPaymentService implements PaymentGateway
{
    public function validate(array $payload): array
    {
        usleep(800000); // délai simulé 800ms
        $success = rand(1, 10) <= 7; // 70% succès

        return [
            'statut'  => $success ? 'VALIDÉ' : 'REJETÉ',
            'message' => $success
                ? 'Paiement approuvé.'
                : "Paiement refusé par l'opérateur.",
            'ref'     => 'MOCK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ];
    }
}
