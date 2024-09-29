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
    private $Return;

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
            if (empty($id)) {
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read();

            $read->ExeRead("RACAS", "WHERE CD_RACA = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self($read->getResult()[0]['DESCRICAO'], $read->getResult()[0]['FL_ATIVO'], $read->getResult()[0]['CD_ESPECIE'], $read->getResult()[0]['CD_RACA']);
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

        $query = "SELECT RACAS.CD_RACA,
                  RACAS.DESCRICAO,
                  ESPECIES.DESCRICAO AS ESPECIE_DESCRICAO,
                  (CASE WHEN RACAS.FL_ATIVO = 1 THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO,
                  COUNT(RACAS.CD_RACA) OVER() AS TOTAL_FILTERED,  
                  (SELECT COUNT(RACAS.CD_RACA) FROM RACAS) AS TOTAL_TABLE 
                  FROM RACAS
                  INNER JOIN ESPECIES ON (RACAS.CD_ESPECIE = ESPECIES.CD_ESPECIE)
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND RACAS.CD_RACA LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND RACAS.DESCRICAO LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaEspecie)) {
            $query .= " AND ESPECIES.DESCRICAO LIKE '%$pesquisaEspecie%'";
        }
        if (!empty($pesquisaAtivo)) {
            $pesquisaAtivo = $pesquisaAtivo == 2 ? 0 : 1;
            $query .= " AND RACAS.FL_ATIVO = $pesquisaAtivo";
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

            if (!$insert->getResult()) {
                throw new Exception($insert->getMessage());
            }

            $insert->Commit();
            $this->codigo = $insert->getLastInsert();

            $logs = new \App\Models\Logs($_SESSION['username'], 'INSERT', 'RACAS', $this->codigo, $dadosInsert);
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

                $logs = new \App\Models\Logs($_SESSION['username'], 'UPDATE', 'RACAS', $this->codigo, $dadosUpdate);
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
            
            $read->FullRead("SELECT * FROM RACAS WHERE CD_RACA = :C", "C=$this->codigo");
            $dadosRaca = $read->getResult()[0];

            $delete->ExeDelete("RACAS", "WHERE CD_RACA = :C", "C=$this->codigo");
            $delete->Commit();

            $logs = new \App\Models\Logs($_SESSION['username'], 'DELETE', 'RACAS', $this->codigo, $dadosRaca);
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
            $especie = !empty($arrayParam['idEspecie']) ? $arrayParam['idEspecie'] : '';

            $read = new \App\Conn\Read();

            $query = "SELECT $colunas FROM RACAS WHERE FL_ATIVO = 1";

            if (!empty($descricao)) {
                $query .= " AND DESCRICAO LIKE '%$descricao%'";
            }
            if (!empty($especie)) {
                $query .= " AND CD_ESPECIE = $especie";
            }


            $query .= " LIMIT 30";

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

    public function getReturn()
    {
        return $this->Return;
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
