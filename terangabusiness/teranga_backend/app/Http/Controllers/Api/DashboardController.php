<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cotisation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $cotisations = Cotisation::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'periode', 'montant', 'statut', 'created_at']);

        $soldeGlobal = Cotisation::where('user_id', $userId)
            ->where('statut', 'VALIDÉ')
            ->sum('montant');

        return response()->json([
            'solde_global'      => (float) $soldeGlobal,
            'cotisations'       => $cotisations,
            'cotisations_count' => $cotisations->count(),
        ]);
    }
}
