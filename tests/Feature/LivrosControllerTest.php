<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Livros;
use App\Models\User;

class LivrosControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user  = User::factory()->create();
        $this->token = $this->user->createToken('TestToken')->plainTextToken;

        $this->actingAs($this->user, 'api');
    }

    public function test_list_sem_filtros_retorna_livros()
    {
        $user = User::factory()->create();

        $livro = Livros::factory()->create(['usuario_publicador_id' => $this->user->id]);


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/v1/livros');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['titulo', 'usuario_publicador', 'indices']
            ]);
    }


    public function test_list_com_filtro_titulo_do_indice()
    {

        Livros::factory()->hasIndices(1, [
            'titulo' => 'Indice Especial',
            'pagina' => 10,
            'indice_pai_id' => null
        ])->create(['usuario_publicador_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/v1/livros?titulo_do_indice=Especial');

        $response->assertStatus(200);
        $this->assertStringContainsString('Especial', $response->json()[0]['indices'][0]['titulo']);
    }

    public function test_add_cria_livro_e_indices()
    {
        $payload = [
            'titulo' => 'Livro Teste',
            'indices' => [
                [
                    'titulo' => 'Indice 1',
                    'pagina' => 1,
                    'subindices' => [
                        ['titulo' => 'Subindice 1.1', 'pagina' => 2]
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/v1/livros', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['titulo' => 'Livro Teste'])
            ->assertJsonStructure(['message', 'livro' => ['indices']]);

        $this->assertDatabaseHas('livros', ['titulo' => 'Livro Teste']);
        $this->assertDatabaseHas('indices', ['titulo' => 'Indice 1']);
        $this->assertDatabaseHas('indices', ['titulo' => 'Subindice 1.1']);
    }

    public function test_addIndiceXML_retorna_erro_xml_invalido()
    {
        $livro = Livros::factory()->create(['usuario_publicador_id' => $this->user->id]);

        $xmlMalformado = '<indice><item titulo="sem fechamento"';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/xml',
        ])->post("/v1/livros/{$livro->id}/importar-indices-xml", [$xmlMalformado]);


        $response->assertStatus(422)
            ->assertJson(['error' => 'XML inválido.']);
    }

    public function test_addIndiceXML_chama_job_dispatch()
    {
        $livro = Livros::factory()->create(['usuario_publicador_id' => $this->user->id]);

        $xmlString = '<indice><item pagina="1" titulo="Teste"/></indice>';

        \Illuminate\Support\Facades\Queue::fake();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/xml',
        ])->postJson("/v1/livros/{$livro->id}/importar-indices-xml", [$xmlString]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Em breve processaremos os indices']);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\LivrosImportarIndicesXMLJob::class);
    }
}
