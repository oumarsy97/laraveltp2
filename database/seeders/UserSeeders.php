<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // Crée un utilisateur avec le rôle admin
        User::factory()->admin()->create();
        // Crée un utilisateur avec le rôle boutiquier
        User::factory()->boutiquier()->create();
    }
}
