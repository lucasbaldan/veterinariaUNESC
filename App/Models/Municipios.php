<?php

namespace App\Models;

use Exception;

class Municipios
{

    private $codigo;
    private $descricao;
    private \App\Models\Estados $estado;

    private $Result;
    private $Message;
    private $Return;

    public function __construct($descricao, $cdIBGEEstado, $codigo = null)
    {
        $this->descricao = $descricao;
        $this->codigo = $codigo;
        $this->estado = \App\Models\Estados::findByIdIBGE($cdIBGEEstado);
    }

    public static function findById($id)
    {
        try {
            if (empty($id)) {
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read();

            $read->ExeRead("CIDADES", "WHERE CD_CIDADE = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self($read->getResult()[0]['NOME'], $read->getResult()[0]['ID_IBGE_ESTADO'], $read->getResult()[0]['CD_CIDADE']);
        } catch (Exception $e) {
            return new self('', '', '');
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
        $pesquisaEstado = $arrayParam['pesquisaEstado'];

        $read = new \App\Conn\Read();

        $query = "SELECT CIDADES.CD_CIDADE,
                  CIDADES.NOME,
                  ESTADOS.NOME AS NOME_ESTADO,
                  COUNT(CIDADES.CD_CIDADE) OVER() AS TOTAL_FILTERED,  
                  (SELECT COUNT(CIDADES.CD_CIDADE) FROM CIDADES) AS TOTAL_TABLE 
                  FROM CIDADES
                  LEFT JOIN ESTADOS ON (CIDADES.ID_IBGE_ESTADO = ESTADOS.CD_IBGE)
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND CIDADES.CD_CIDADE LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND CIDADES.NOME LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaEstado)) {
            $query .= " AND ESTADOS.NOME LIKE '%$pesquisaEstado%'";
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

            $dadosInsert = ["CD_CIDADE" => $this->codigo, "NOME" => $this->descricao, "ID_IBGE_ESTADO" => $this->estado->getCodigoIbge()];
            $insert->ExeInsert("CIDADES", $dadosInsert);

            if (!$insert->getResult()) {
                throw new Exception($insert->getMessage());
            }

            $insert->Commit();
            $this->codigo = $insert->getLastInsert();

            $logs = new \App\Models\Logs($_SESSION['username'], 'INSERT', 'CIDADES', $this->codigo, $dadosInsert);
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

            $read->ExeRead("CIDADES", "WHERE CD_CIDADE = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $conn = \App\Conn\Conn::getConn();
                $update = new \App\Conn\Update($conn);

                $dadosUpdate = ["CD_CIDADE" => $this->codigo, "NOME" => $this->descricao, "ID_IBGE_ESTADO" => $this->estado->getCodigoIbge()];

                $update->ExeUpdate("CIDADES", $dadosUpdate, "WHERE CD_CIDADE = :D", "D=$this->codigo");

                if (!$update->getResult()) {
                    throw new Exception($update->getMessage());
                }
                $update->Commit();

                $logs = new \App\Models\Logs($_SESSION['username'], 'UPDATE', 'CIDADES', $this->codigo, $dadosUpdate);
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
            
            $read->FullRead("SELECT * FROM CIDADES WHERE CD_CIDADE = :C", "C=$this->codigo");
            $dadosCidade = $read->getResult()[0];

            $delete->ExeDelete("CIDADES", "WHERE CD_CIDADE = :C", "C=$this->codigo");
            $delete->Commit();

            $logs = new \App\Models\Logs($_SESSION['username'], 'DELETE', 'CIDADES', $this->codigo, $dadosCidade);
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
            $innerJoin = !empty($arrayParam['innerJoin']) ? $arrayParam['innerJoin'] : '';

            $read = new \App\Conn\Read();

            $query = "SELECT $colunas FROM CIDADES $innerJoin WHERE 1=1";

            if (!empty($descricao)) {
                $query .= " AND CIDADES.NOME LIKE '%$descricao%'";
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
    public function getDescricao()
    {
        return $this->descricao;
    }
    public function getEstado()
    {
        return $this->estado;
    }
}
