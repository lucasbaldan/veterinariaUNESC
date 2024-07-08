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

        public static function FormataDataDoBanco($date) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            list($year, $month, $day) = explode('-', $date);
            return "$day/$month/$year";
        } else {
            return $date;
        }
        }
}
