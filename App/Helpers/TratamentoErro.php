<?php

namespace App\Helpers;

class TratamentoErro
{

    public static function SqlError(string $erro) {
        if(strpos($erro, 'Duplicate entry')) return "Registro já cadastrado para o código informado";
        else return $erro;
    }
}
