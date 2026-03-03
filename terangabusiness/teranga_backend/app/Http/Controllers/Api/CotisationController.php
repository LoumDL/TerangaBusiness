<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cotisation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CotisationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cotisations = Cotisation::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $solde = Cotisation::where('user_id', $request->user()->id)
            ->where('statut', 'VALIDÉ')
            ->sum('montant');

        return response()->json([
            'cotisations' => $cotisations,
            'solde_global' => (float) $solde,
            'total' => $cotisations->count(),
        ]);
    }
}
