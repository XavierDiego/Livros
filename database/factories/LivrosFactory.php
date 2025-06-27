<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livros>
 */
class LivrosFactory extends Factory
{

    public function definition(): array
    {
        return [
            'usuario_publicador_id' => User::factory(),
            'titulo' => fake()->sentence(3),
        ];
    }
}
