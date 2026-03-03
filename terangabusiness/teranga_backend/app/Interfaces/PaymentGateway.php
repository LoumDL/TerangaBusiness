<?php

namespace App\Interfaces;

interface PaymentGateway
{
    public function validate(array $payload): array;
}
