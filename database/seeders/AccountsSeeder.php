<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AccountsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Create Admin
        User::firstOrCreate(
            ['email' => 'judealmaden2045@gmail.com'],
            [
                'name' => 'Jude Almaden',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Create Players (Teams 1–6)
        foreach (range(1, 6) as $i) {
            User::firstOrCreate(
                ['email' => "player{$i}@gmail.com"],
                [
                    'name' => "Team {$i}",
                    'password' => Hash::make('password123'),
                ]
            );
        }
    }
}
