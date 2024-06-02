<?php

namespace App\Models;

use Exception;

class TipoAnimais
{

    private $codigo;
    private $descricao;
    private $ativo;

    private $Result;
    private $Message;

    public function __construct($descricao, $ativo, $codigo = null)
    {
        $this->descricao = $descricao;
        $this->ativo = $ativo == 2 ? 0 : $ativo;
        $this->codigo = $codigo;
    }

    public function findById()
    {
        try {
            $read = new \App\Conn\Read();

            $read->ExeRead("TIPO_ANIMAL", "WHERE CD_TIPO_ANIMAL = :C LIMIT 1", "C=$this->codigo");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            $this->codigo = $read->getResult()[0]['cd_tipo_animal'];
            $this->descricao = $read->getResult()[0]['descricao'];
            $this->ativo = $read->getResult()[0]['fl_ativo'];

            $this->Result = true;
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
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

        if (!empty($pesquisaCodigo)) {
            $query .= " AND tipo_animal.cd_tipo_animal LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND tipo_animal.descricao LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaAtivo)) {
            $pesquisaAtivo = $pesquisaAtivo == 2 ? 0 : 1;
            $query .= " AND tipo_animal.fl_ativo = $pesquisaAtivo";
        }

        if (!empty($orderBy)) {
            $query .= " ORDER BY $orderBy $orderAscDesc";
        }

        $query .= " LIMIT $start, $limit";

        $read->FullRead($query);

        return $read->getResult();
    }

    public function Inserir()
    {

        try {
            $conn = \App\Conn\Conn::getConn();
            $insert = new \App\Conn\Insert($conn);

            $dadosInsert = ["CD_TIPO_ANIMAL" => $this->codigo, "DESCRICAO" => $this->descricao, "FL_ATIVO" => $this->ativo];
            $insert->ExeInsert("TIPO_ANIMAL", $dadosInsert);

            if(!$insert->getResult()){
                throw new Exception($insert->getMessage());
            }

            $insert->Commit();
            $this->Result = true;
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
            $insert->Rollback();
        }
    }

    public function Atualizar()
    {
        try {
            $read = new \App\Conn\Read();

            $read->ExeRead("TIPO_ANIMAL", "WHERE CD_TIPO_ANIMAL = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $conn = \App\Conn\Conn::getConn();
                $update = new \App\Conn\Update($conn);

                $dadosUpdate = ["CD_TIPO_ANIMAL" => $this->codigo, "DESCRICAO" => $this->descricao, "FL_ATIVO" => $this->ativo];

                $update->ExeUpdate("TIPO_ANIMAL", $dadosUpdate, "WHERE CD_TIPO_ANIMAL = :D", "D=$this->codigo");

                if (!$update->getResult()) {
                    throw new Exception($update->getMessage());
                }
                $update->Commit();
                $this->Result = true;
            } else {
                throw new Exception("Ops, Parece que esse registro não existe mais na base de dados!");
            }
        } catch (Exception $e) {
            $update->Rollback();
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function Excluir()
    {

        try {
            $conn = \App\Conn\Conn::getConn();
            $delete = new \App\Conn\Delete($conn);

            $delete->ExeDelete("TIPO_ANIMAL", "WHERE CD_TIPO_ANIMAL = :C", "C=$this->codigo");

            $delete->Commit();
            $this->Result = true;
        } catch (Exception $e) {
            $this->Message = $e->getMessage();
            $delete->Rollback();
            $this->Result = false;
        }
    }


    public function getResult()
    {
        return $this->Result;
    }

    public function getMessage()
    {
        return $this->Message;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }

    // Método getter para $descricao
    public function getDescricao()
    {
        return $this->descricao;
    }

    // Método getter para $ativo
    public function getAtivo()
    {
        return $this->ativo;
    }
}
