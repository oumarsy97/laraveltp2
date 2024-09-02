<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::factory()->create([
            'libelle' => 'ADMIN',
        ]);

        Role::factory()->create([
            'libelle' => 'BOUTIQUIER',
        ]);

        Role::factory()->create([
            'libelle' => 'CLIENT',
        ]);
    }
}
