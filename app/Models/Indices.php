<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indices extends Model
{

    protected $table = 'indices';

    protected $fillable = [
        'livro_id',
        'indice_pai_id',
        'titulo',
        'pagina'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'id',
        'livro_id',
        'indice_pai_id'
    ];

    public function subindices()
    {
        return $this->hasMany(Indices::class, 'indice_pai_id');
    }

    public function livro()
    {
        return $this->belongsTo(Livros::class, 'livro_id');
    }

}
