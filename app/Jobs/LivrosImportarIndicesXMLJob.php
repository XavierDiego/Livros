<?php

namespace App\Jobs;

use App\Models\Indices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LivrosImportarIndicesXMLJob implements ShouldQueue
{
    use Queueable;

    protected $livrosId;
    protected $xml;

    /**
     * Create a new job instance.
     */
    public function __construct($livrosId, $xml)
    {
        $this->livrosId = $livrosId;
        $this->xml      = $xml;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $xml = simplexml_load_string($this->xml);

        $this->save($xml);
    }

    private function save($xmlItems, $indicePaiId = null)
    {
        foreach ($xmlItems->item as $item) {
            $indice                = new Indices();
            $indice->livro_id      = $this->livrosId;
            $indice->indice_pai_id = $indicePaiId;
            $indice->titulo        = (string) $item['titulo'];
            $indice->pagina        = (int) $item['pagina'];

            if ($indice->save()) {
                if ($item->item) {
                    $this->save($item, $indice->id);
                }
            }
        }
    }
}
