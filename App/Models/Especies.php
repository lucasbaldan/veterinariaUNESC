<?php

namespace App\Models;

use Exception;

class Especies
{

    private $codigo;
    private $descricao;
    private $ativo;

    private $Result;
    private $Message;
    private $Return;

    public function __construct($descricao, $ativo,  $codigo = null)
    {
        $this->descricao = $descricao;
        $this->ativo = $ativo == 2 ? 0 : $ativo;
        $this->codigo = $codigo;
    }

    public static function findById($id)
    {
        try {
            if (empty($id)) {
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read();

            $read->ExeRead("ESPECIES", "WHERE CD_ESPECIE = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self($read->getResult()[0]['DESCRICAO'], $read->getResult()[0]['FL_ATIVO'], $read->getResult()[0]['CD_ESPECIE']);
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

        $read = new \App\Conn\Read();

        $query = "SELECT ESPECIES.CD_ESPECIE,
                  ESPECIES.DESCRICAO,
                  (CASE WHEN ESPECIES.FL_ATIVO = 1 THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO,
                  COUNT(ESPECIES.CD_ESPECIE) OVER() AS TOTAL_FILTERED,  
                  (SELECT COUNT(ESPECIES.CD_ESPECIE) FROM ESPECIES) AS TOTAL_TABLE 
                  FROM ESPECIES
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND ESPECIES.CD_ESPECIE LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND ESPECIES.DESCRICAO LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaAtivo)) {
            $pesquisaAtivo = $pesquisaAtivo == 2 ? 0 : 1;
            $query .= " AND ESPECIES.FL_ATIVO = $pesquisaAtivo";
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

            if (!$insert->getResult()) {
                throw new Exception($insert->getMessage());
            }

            $insert->Commit();
            $this->codigo = $insert->getLastInsert();

            $logs = new \App\Models\Logs($_SESSION['username'], 'INSERT', 'ESPECIES', $this->codigo, $dadosInsert);
            $logs->Insert();

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

                $logs = new \App\Models\Logs($_SESSION['username'], 'UPDATE', 'ESPECIES', $this->codigo, $dadosUpdate);
                $logs->Insert();

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
            $read = new \App\Conn\Read($conn);
            
            $read->FullRead("SELECT * FROM ESPECIES WHERE CD_ESPECIE = :C", "C=$this->codigo");
            $dadosEspecie = $read->getResult()[0];

            $delete->ExeDelete("ESPECIES", "WHERE CD_ESPECIE = :C", "C=$this->codigo");
            $delete->Commit();

            $logs = new \App\Models\Logs($_SESSION['username'], 'DELETE', 'ESPECIES', $this->codigo, $dadosEspecie);
            $logs->Insert();

            $this->Result = true;
        } catch (Exception $e) {
            $this->Message = $e->getMessage();
            $delete->Rollback();
            $this->Result = false;
        }
    }

    public function generalSearch($arrayParam)
    {
        try {
            $colunas = $arrayParam['colunas'];
            $descricao = !empty($arrayParam['descricaoPesquisa']) ? $arrayParam['descricaoPesquisa'] : '';

            $read = new \App\Conn\Read();

            $query = "SELECT $colunas FROM ESPECIES WHERE FL_ATIVO = 1 ";

            if (!empty($descricao)) {
                $query .= " AND DESCRICAO LIKE '%$descricao%'";
            }

            $read->FullRead($query);
            $this->Result = true;
            $this->Return = $read->getResult();
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
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
    public function getReturn()
    {
        return $this->Return;
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
