<?php

namespace App\Models;

use Exception;

class Especies
{

    private $codigo;
    private $descricao;
    private \App\Models\TipoAnimais $tipoAnimal;
    private $ativo;

    private $Result;
    private $Message;

    public function __construct($descricao, $ativo, $cdTipoAnimal, $codigo = null)
    {
        $this->descricao = $descricao;
        $this->ativo = $ativo == 2 ? 0 : $ativo;
        $this->codigo = $codigo;
        
        $this->tipoAnimal = new \App\Models\TipoAnimais('', '',$cdTipoAnimal);
        $this->tipoAnimal->findById();
    }

    public function findById()
    {
        try {
            $read = new \App\Conn\Read();

            $read->ExeRead("ESPECIES", "WHERE CD_ESPECIE = :C LIMIT 1", "C=$this->codigo");

            if (!$read->getResult()) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            $this->codigo = $read->getResult()[0]['cd_especie'];
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

        $query = "SELECT especies.cd_especie,
                  especies.descricao,
                  (CASE WHEN especies.fl_ativo = 1 THEN 'Sim' ELSE 'Não' END) as fl_ativo, 
                  COUNT(especies.cd_especie) OVER() AS total_filtered,  
                  (SELECT COUNT(especies.cd_especie) FROM especies) AS total_table 
                  FROM especies
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND especies.cd_especie LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND especies.descricao LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaAtivo)) {
            $pesquisaAtivo = $pesquisaAtivo == 2 ? 0 : 1;
            $query .= " AND especies.fl_ativo = $pesquisaAtivo";
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

            $dadosInsert = ["CD_ESPECIE" => $this->codigo, "DESCRICAO" => $this->descricao, "FL_ATIVO" => $this->ativo];
            $insert->ExeInsert("ESPECIES", $dadosInsert);

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

            $read->ExeRead("ESPECIES", "WHERE CD_ESPECIE = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $conn = \App\Conn\Conn::getConn();
                $update = new \App\Conn\Update($conn);

                $dadosUpdate = ["CD_ESPECIE" => $this->codigo, "DESCRICAO" => $this->descricao, "FL_ATIVO" => $this->ativo];

                $update->ExeUpdate("ESPECIES", $dadosUpdate, "WHERE CD_ESPECIE = :D", "D=$this->codigo");

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

            $delete->ExeDelete("ESPECIES", "WHERE CD_ESPECIE = :C", "C=$this->codigo");

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
