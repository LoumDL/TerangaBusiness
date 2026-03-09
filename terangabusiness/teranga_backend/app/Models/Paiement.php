<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'montant',
        'statut',
        'paydunya_token',
        'checkout_url',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function justificatif(): HasOne
    {
        return $this->hasOne(Justificatif::class);
    }
}
