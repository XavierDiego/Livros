<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\LivrosImportarIndicesXMLJob;
use App\Models\Indices;
use App\Models\Livros;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LivrosController extends Controller
{
    public function list(Request $request)
    {
        $params = $request->all();

        $livroModel = Livros::with(['usuario_publicador']);

        if (!empty($params['titulo_do_indice'])) {
            $tituloI = filter_var($params['titulo_do_indice'], FILTER_SANITIZE_STRING);

            $livroModel->whereHas('indices', function ($query) use ($tituloI) {
                $query->where('titulo', 'like', '%' . $tituloI . '%');
            });

            $livroModel->with(['indices' => function ($query) use ($tituloI) {
                $query->where('titulo', 'like', '%' . $tituloI . '%')
                    ->with('subindices');
            }]);
        } else {
            $livroModel->with('indices.subindices');
        }

        if (!empty($params['titulo'])) {
            $titulo = filter_var($params['titulo'], FILTER_SANITIZE_STRING);
            $livroModel->where('titulo', 'like', '%' . $titulo . '%');
        }

        $livros = $livroModel->get();

        return response()->json($livros);
    }

    public function add(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string',
            'indices' => 'array',
        ]);

        $livro = Livros::create([
            'usuario_publicador_id' => auth()->id(),
            'titulo' => $request->titulo,
        ]);

        foreach ($request->indices ?? [] as $indiceData) {
            $this->salvarIndiceRecursivo($livro, null, $indiceData);
        }

        return response()->json([
            'message' => 'Livro e índices criados com sucesso.',
            'livro' => $livro->load('indices'),
        ], 201);

    }

    private function salvarIndiceRecursivo(Livros $livro, ?Indices $pai = null, array $dados)
    {
        $indice = $livro->indices()->create([
            'titulo' => $dados['titulo'],
            'pagina' => $dados['pagina'] ?? null,
            'indice_pai_id' => $pai?->id,
        ]);

        if (!empty($dados['subindices'])) {
            foreach ($dados['subindices'] as $sub) {
                $this->salvarIndiceRecursivo($livro, $indice, $sub);
            }
        }

        return $indice;
    }

    public function addIndiceXML(Request $request, $livroId)
    {
        $xmlString = $request->getContent();
        $xmlString = trim($xmlString, "[]\"");

        $xmlString = stripslashes($xmlString);
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString);

        if ($xml === false) {
            return response()->json(['error' => 'XML inválido.'], 422);
        }

        $livros = Livros::find($livroId);

        if (!$livros) {
            return response()->json(['error' => 'Id inválido'], 404);
        }

        if ($request->has('sync') && $request->get('sync') == 1) {
            (new LivrosImportarIndicesXMLJob($livros->id, $xmlString))->handle();
            $message = 'Já procesamos os indices de forma síncrona';

        } else {
            LivrosImportarIndicesXMLJob::dispatch($livros->id, $xmlString);
            $message = 'Em breve processaremos os indices';
        }


        return response()->json([
            'message' => $message,
        ], 200);
    }
}
