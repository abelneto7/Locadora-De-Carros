<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

    public function rules(){
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.':marcas',
            'imagem' => 'required|file|mimes:png,jpg'
        ];
    }

    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório',
            'imagem.mimes' => 'O aqruivo deve ser do tipo PNG ou JPG',
            'nome.unique' => 'O nome da marca já existe'
        ];
    }

    public function modelos () {
        // um modelo pertence a uma marca
        return $this->hasMany('App\Models\Modelo');
    }
}
