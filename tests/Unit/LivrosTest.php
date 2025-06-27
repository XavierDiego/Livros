<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Livros;
use Tests\TestCase;


class LivrosTest extends TestCase
{

    use RefreshDatabase;

    public function test_create(): void
    {
        $livro = Livros::factory()->create();

        $this->assertDatabaseHas('livros', [
            'id' => $livro->id,
            'titulo' => $livro->titulo,
        ]);
    }
}
