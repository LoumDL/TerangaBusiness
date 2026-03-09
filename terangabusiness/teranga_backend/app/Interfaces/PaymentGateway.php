<?php

namespace App\Interfaces;

interface PaymentGateway
{
    /**
     * Initie un paiement et retourne l'URL de checkout.
     * @param array $payload ['paiement_id', 'montant', 'description', 'user_email', 'user_name']
     * @return array ['token' => string, 'checkout_url' => string]
     */
    public function initiate(array $payload): array;

    /**
     * Vérifie le statut d'un paiement via son token.
     * @return array ['statut' => 'VALIDÉ|REJETÉ|EN_ATTENTE', 'ref' => string, 'message' => string]
     */
    public function verify(string $token): array;

    /**
     * Charge directement via mobile money sans redirection.
     * @param string $token    Token de l'invoice PayDunya
     * @param string $phone    Numéro de téléphone (ex: 771234567)
     * @param string $provider wave_senegal | orange_money_senegal_merci
     * @return array ['statut' => 'EN_ATTENTE|REJETÉ', 'message' => string]
     */
    public function directCharge(string $token, string $phone, string $provider): array;
}
