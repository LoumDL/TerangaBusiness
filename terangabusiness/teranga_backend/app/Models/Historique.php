<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historique extends Model
{
    use HasFactory;

    protected $table = 'historique';

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'montant',
        'statut',
        'date',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date'    => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
