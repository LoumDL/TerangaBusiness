<?php

namespace Database\Seeders;

use App\Models\Historique;
use App\Models\Justificatif;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaiementSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'demo@teranga.sn')->first();
        if (! $user) {
            return;
        }

        $paiements = [
            [
                'description' => 'Cotisation mois de juillet 2026',
                'montant'     => 20000.00,
                'statut'      => 'VALIDÉ',
                'date'        => '2026-07-05',
            ],
            [
                'description' => 'Cotisation mois d\'août 2026',
                'montant'     => 20000.00,
                'statut'      => 'REJETÉ',
                'date'        => '2026-08-03',
            ],
        ];

        foreach ($paiements as $data) {
            $paiement = Paiement::firstOrCreate(
                ['user_id' => $user->id, 'description' => $data['description']],
                [
                    'montant'    => $data['montant'],
                    'statut'     => $data['statut'],
                    'created_at' => $data['date'],
                    'updated_at' => $data['date'],
                ]
            );

            Justificatif::firstOrCreate(
                ['paiement_id' => $paiement->id],
                [
                    'file_url'      => '/storage/justificatifs/demo-justificatif.pdf',
                    'file_type'     => 'application/pdf',
                    'original_name' => 'justificatif-demo.pdf',
                    'uploaded_at'   => $data['date'],
                ]
            );

            Historique::firstOrCreate(
                ['user_id' => $user->id, 'type' => 'PAIEMENT', 'description' => $data['description']],
                [
                    'montant' => $data['montant'],
                    'statut'  => $data['statut'],
                    'date'    => $data['date'],
                ]
            );
        }
    }
}
