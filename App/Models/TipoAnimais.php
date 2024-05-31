<?php

namespace App\Models;

use Exception;

class TipoAnimais
{

    private $codigo;
    private $descricao;
    private $ativo;

    public function __construct($descricao, $ativo, $codigo = null)
    {
        $this->descricao = $descricao;
        $this->ativo = $ativo;
        $this->codigo = $codigo;
    }

    public static function SelectGrid($arrayParam)
    {

        $start = $arrayParam['inicio'];
        $limit = $arrayParam['limit'];
        $orderBy = $arrayParam['orderBy'];
        $orderAscDesc = $arrayParam['orderAscDesc'];
        $pesquisaCodigo = $arrayParam['pesquisaCodigo'];
        $pesquisaDescricao = $arrayParam['pesquisaDescricao'];
        $pesquisaAtivo = $arrayParam['pesquisaAtivo'];

        $read = new \App\Conn\Read();

        $query = "SELECT tipo_animal.cd_tipo_animal,
                  tipo_animal.descricao,
                  (CASE WHEN tipo_animal.fl_ativo = 1 THEN 'Sim' ELSE 'Não' END) as fl_ativo, 
                  COUNT(tipo_animal.cd_tipo_animal) OVER() AS total_filtered,  
                  (SELECT COUNT(tipo_animal.cd_tipo_animal) FROM tipo_animal) AS total_table 
                  FROM tipo_animal
                  WHERE 1=1";

$bindParams = "";

        if(!empty($pesquisaCodigo)){
            $query .= " AND tipo_animal.cd_tipo_animal LIKE '%$pesquisaCodigo%'";
        }
        if(!empty($pesquisaDescricao)){
            $query .= " AND tipo_animal.descricao LIKE '%$pesquisaDescricao%'";
        }
        if(!empty($pesquisaAtivo)){
            $query .= " AND tipo_animal.fl_ativo LIKE '%$pesquisaAtivo%'";
        }

        if (!empty($orderBy)) {
            $query .= " ORDER BY $orderBy $orderAscDesc";
        }

        $query .= " LIMIT $start, $limit";

        $read->FullRead($query);

        return $read->getResult();
    }
}
