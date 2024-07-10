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
    private $Return;

    public function __construct($descricao, $ativo, $cdTipoAnimal, $codigo = null)
    {
        $this->descricao = $descricao;
        $this->ativo = $ativo == 2 ? 0 : $ativo;
        $this->codigo = $codigo;
        $this->tipoAnimal = \App\Models\TipoAnimais::findById($cdTipoAnimal);
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

            return new self($read->getResult()[0]['DESCRICAO'], $read->getResult()[0]['FL_ATIVO'], $read->getResult()[0]['ID_TIPO_ANIMAL'], $read->getResult()[0]['CD_ESPECIE']);
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
        $pesquisaTipoAnimal = $arrayParam['pesquisaTipoAnimal'];

        $read = new \App\Conn\Read();

        $query = "SELECT ESPECIES.CD_ESPECIE,
                  ESPECIES.DESCRICAO,
                  TIPO_ANIMAL.DESCRICAO AS TIPO_ANIMAL_DESCRICAO,
                  (CASE WHEN ESPECIES.FL_ATIVO = 1 THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO,
                  COUNT(ESPECIES.CD_ESPECIE) OVER() AS TOTAL_FILTERED,  
                  (SELECT COUNT(ESPECIES.CD_ESPECIE) FROM ESPECIES) AS TOTAL_TABLE 
                  FROM ESPECIES
                  INNER JOIN TIPO_ANIMAL ON (ESPECIES.ID_TIPO_ANIMAL = TIPO_ANIMAL.CD_TIPO_ANIMAL)
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND ESPECIES.CD_ESPECIE LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND ESPECIES.DESCRICAO LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaTipoAnimal)) {
            $query .= " AND TIPO_ANIMAL.DESCRICAO LIKE '%$pesquisaTipoAnimal%'";
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

            $dadosInsert = ["CD_ESPECIE" => $this->codigo, "DESCRICAO" => $this->descricao, "ID_TIPO_ANIMAL" => $this->tipoAnimal->getCodigo(), "FL_ATIVO" => $this->ativo];
            $insert->ExeInsert("ESPECIES", $dadosInsert);

            if (!$insert->getResult()) {
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

                $dadosUpdate = ["CD_ESPECIE" => $this->codigo, "DESCRICAO" => $this->descricao, "ID_TIPO_ANIMAL" => $this->tipoAnimal->getCodigo(), "FL_ATIVO" => $this->ativo];

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

    public function generalSearch($arrayParam)
    {
        try {
            $colunas = $arrayParam['colunas'];
            $descricao = !empty($arrayParam['descricaoPesquisa']) ? $arrayParam['descricaoPesquisa'] : '';
            $tipoAnimal = !empty($arrayParam['TipoAnimal']) ? $arrayParam['TipoAnimal'] : '';

            $read = new \App\Conn\Read();

            $query = "SELECT $colunas FROM ESPECIES WHERE 1=1";

            if (!empty($descricao)) {
                $query .= " AND DESCRICAO LIKE '%$descricao%'";
            }

            if (!empty($tipoAnimal)) {
                $query .= " AND ID_TIPO_ANIMAL = $tipoAnimal";
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

    public function getTipoAnimal()
    {
        return $this->tipoAnimal;
    }

    // Método getter para $ativo
    public function getAtivo()
    {
        return $this->ativo;
    }
}
