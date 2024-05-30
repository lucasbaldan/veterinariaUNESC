<?php

namespace App\Models;
use Exception;

class TipoAnimais{
    
    private $codigo;
    private $descricao;
    private $ativo;

    public function __construct($descricao, $ativo, $codigo = null)
    {
        $this->descricao = $descricao;
        $this->ativo = $ativo;
        $this->codigo = $codigo;

    }

    public static function SelectGrid($arrayParam){

        $start = $arrayParam['inicio'];
        $limit = $arrayParam['limit'];
        $orderBy = $arrayParam['orderBy'] == 1 ? "cd_tipo_animal" : ($arrayParam['orderBy'] == 2 ? "descricao" : "fl_ativo");
        $orderAscDesc = $arrayParam['orderAscDesc'];

        $read = new \App\Conn\Read();

        $read->FullRead("SELECT * FROM tipo_animal
                        ORDER BY :OB :AD
                        LIMIT $start, $limit",
                        "OB=$orderBy&AD=$orderAscDesc");

        if ($read->getRowCount() == 0) {
            return null;
        }
        return $return = [$read->getResult(), $read->getRowCount()];
    }
}