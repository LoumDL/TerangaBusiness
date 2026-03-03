<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Historique;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $transactions = Historique::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn ($item) => [
                'id'          => $item->id,
                'type'        => $item->type,
                'description' => $item->description,
                'montant'     => (float) $item->montant,
                'statut'      => $item->statut,
                'date'        => $item->date?->toIso8601String(),
            ]);

        return response()->json([
            'transactions' => $transactions,
            'total'        => $transactions->count(),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $item = Historique::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Transaction supprimée.']);
    }
}
