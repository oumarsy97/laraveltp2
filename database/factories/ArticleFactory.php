<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $aliments = [
            'Pomme 500g', 'Banane 400g', 'Carotte 500g', 'Tomate 1kg', 'Pomme de terre',
            'Pain brioche', 'Laicran 1kg', 'Fromage', 'Poulet 500g',
             'Riz Parfume', 'Riz Basmati', 'Riz Millet',
        ];

        return [
            'libelle' => $this->faker->randomElement($aliments),
            'qteStock' => $this->faker->numberBetween(0, 100),
            'prix' => $this->faker->numberBetween(0, 100),
        ];
    }
}
