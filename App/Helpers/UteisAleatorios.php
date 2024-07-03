<?php

namespace App\Helpers;

class UteisAleatorios
{

    public static function removerDadosChave(array $array, $key): array {
        if (array_key_exists($key, $array)) {
            unset($array[$key]);
        }
        return $array;
    }
}
