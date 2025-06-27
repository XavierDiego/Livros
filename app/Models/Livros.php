<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livros extends Model
{

    use HasFactory;

    protected $table = 'livros';


    protected $fillable = [
        'titulo',
        'usuario_publicador_id',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'usuario_publicador_id',
        'id'
    ];

    public function usuario_publicador()
    {
        return $this->belongsTo(User::class, 'usuario_publicador_id');
    }

    public function indices()
    {
        return $this->hasMany(Indices::class, 'livro_id')->whereNull('indice_pai_id');
    }

}
