<?php

namespace Database\Seeders;

use App\Models\Cotisation;
use App\Models\Historique;
use App\Models\User;
use Illuminate\Database\Seeder;

class CotisationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'demo@teranga.sn')->first();
        if (! $user) {
            return;
        }

        $mois = [
            '2026-01' => '2026-01-05',
            '2026-02' => '2026-02-05',
            '2026-03' => '2026-03-05',
            '2026-04' => '2026-04-05',
            '2026-05' => '2026-05-05',
            '2026-06' => '2026-06-05',
        ];

        $nomsMois = [
            '2026-01' => 'janvier 2026',
            '2026-02' => 'février 2026',
            '2026-03' => 'mars 2026',
            '2026-04' => 'avril 2026',
            '2026-05' => 'mai 2026',
            '2026-06' => 'juin 2026',
        ];

        foreach ($mois as $periode => $date) {
            $cot = Cotisation::firstOrCreate(
                ['user_id' => $user->id, 'periode' => $periode],
                [
                    'montant'    => 20000.00,
                    'statut'     => 'VALIDÉ',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );

            Historique::firstOrCreate(
                ['user_id' => $user->id, 'type' => 'COTISATION', 'description' => 'Cotisation ' . $nomsMois[$periode]],
                [
                    'montant' => 20000.00,
                    'statut'  => 'VALIDÉ',
                    'date'    => $date,
                ]
            );
        }
    }
}
