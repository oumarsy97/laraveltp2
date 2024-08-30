<?php

namespace Database\Factories;

use App\Enums\EnumRole;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prenom' => $this->faker->firstName,
            'nom' => $this->faker->lastName,
            'login' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('Password123@'), // Utilise une valeur par défaut pour le mot de passe
            'role_id' => $this->faker->randomElement([1,2,3]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => RoleEnum::ADMIN, // Définit le rôle comme admin
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function boutiquier(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => RoleEnum::BOUTIQUIER, // Définit le rôle comme boutiquier
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function client(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => RoleEnum::CLIENT, // Définit le rôle comme client
            ];
        });
    }

}
