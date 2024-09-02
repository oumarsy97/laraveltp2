<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;



class ClientCompteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
        // Crée trois utilisateurs avec des clients associés
        User::factory(3)->client()->create()->each(function ($user) {
            $client = Client::factory()->makeOne();
            $user->client()->save($client);

        });
    }
}
