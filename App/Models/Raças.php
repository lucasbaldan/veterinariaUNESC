<?php

namespace App\Models;

use Exception;

class Raças
{

    private $codigo;
    private $descricao;
    private \App\Models\Especies $especie;
    private $ativo;

    private $Result;
    private $Message;

    public function __construct($descricao, $ativo, $cdEspecie, $codigo = null)
    {
        $this->descricao = $descricao;
        $this->ativo = $ativo == 2 ? 0 : $ativo;
        $this->codigo = $codigo;
        $this->especie = \App\Models\Especies::findById($cdEspecie);
    }

    public static function findById($id)
    {
        try {
            if(empty($id)){
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read();

            $read->ExeRead("RACAS", "WHERE CD_RACA = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self($read->getResult()[0]['descricao'], $read->getResult()[0]['fl_ativo'], $read->getResult()[0]['cd_especie'], $read->getResult()[0]['cd_raca']);

        } catch (Exception $e) {
            return new self('', '', '', '');
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
        $pesquisaEspecie = $arrayParam['pesquisaEspecie'];

        $read = new \App\Conn\Read();

        $query = "SELECT racas.cd_raca,
                  racas.descricao,
                  especies.descricao as especie_descricao,
                  (CASE WHEN racas.fl_ativo = 1 THEN 'Sim' ELSE 'Não' END) as fl_ativo,
                  COUNT(racas.cd_raca) OVER() AS total_filtered,  
                  (SELECT COUNT(racas.cd_raca) FROM racas) AS total_table 
                  FROM racas
                  INNER JOIN especies ON (racas.cd_especie = especies.cd_especie)
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND racas.cd_raca LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND racas.descricao LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaEspecie)) {
            $query .= " AND especies.descricao LIKE '%$pesquisaEspecie%'";
        }
        if (!empty($pesquisaAtivo)) {
            $pesquisaAtivo = $pesquisaAtivo == 2 ? 0 : 1;
            $query .= " AND racas.fl_ativo = $pesquisaAtivo";
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

            $dadosInsert = ["CD_RACA" => $this->codigo, "DESCRICAO" => $this->descricao, "CD_ESPECIE" => $this->especie->getCodigo(), "FL_ATIVO" => $this->ativo];
            $insert->ExeInsert("RACAS", $dadosInsert);

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

            $read->ExeRead("RACAS", "WHERE CD_RACA = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $conn = \App\Conn\Conn::getConn();
                $update = new \App\Conn\Update($conn);

                $dadosUpdate = ["CD_RACA" => $this->codigo, "DESCRICAO" => $this->descricao, "CD_ESPECIE" => $this->especie->getCodigo(), "FL_ATIVO" => $this->ativo];

                $update->ExeUpdate("RACAS", $dadosUpdate, "WHERE CD_RACA = :D", "D=$this->codigo");

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

            $delete->ExeDelete("RACAS", "WHERE CD_RACA = :C", "C=$this->codigo");

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

    public function getEspecie()
    {
        return $this->especie;
    }

    // Método getter para $ativo
    public function getAtivo()
    {
        return $this->ativo;
    }
}
