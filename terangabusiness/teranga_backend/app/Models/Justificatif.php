<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Justificatif extends Model
{
    use HasFactory;

    protected $fillable = [
        'paiement_id',
        'file_url',
        'file_type',
        'original_name',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }
}
