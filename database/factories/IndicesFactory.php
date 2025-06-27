<?php

namespace Database\Factories;

use App\Models\Indices;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndicesFactory extends Factory
{
    protected $model = Indices::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->words(3, true),
            'pagina' => $this->faker->numberBetween(1, 100),
            'indice_pai_id' => null,
            'livro_id' => null,
        ];
    }

    public function hasSubindices($count = 1)
    {
        return $this->has(
            Indices::factory()->count($count)->state(function (array $attributes, Indices $parent) {
                return ['indice_pai_id' => $parent->id];
            }),
            'subindices'
        );
    }
}
