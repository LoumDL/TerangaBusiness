<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@teranga.sn'],
            [
                'name'     => 'Membre Démo',
                'email'    => 'demo@teranga.sn',
                'password' => Hash::make('Demo@2026'),
            ]
        );
    }
}
