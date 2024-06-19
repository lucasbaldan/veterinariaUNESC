<?php

namespace App\Models;

use Exception;

class Bairros
{

    private $codigo;
    private $nome;

    private $Result;
    private $Message;
    private $Return;

    public function __construct($descricao, $codigo = null)
    {
        $this->nome = $descricao;
        $this->codigo = $codigo;
    }

    public static function findById($id)
    {
        try {
            if(empty($id)){
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read();

            $read->ExeRead("BAIRROS", "WHERE CD_BAIRRO = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self($read->getResult()[0]['nome'], $read->getResult()[0]['cd_bairro']);

        } catch (Exception $e) {
            return new self('', '');
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

        $read = new \App\Conn\Read();

        $query = "SELECT bairros.cd_bairro,
                  bairros.nome,
                  COUNT(bairros.cd_bairro) OVER() AS total_filtered,  
                  (SELECT COUNT(bairros.cd_bairro) FROM bairros) AS total_table 
                  FROM bairros
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND bairros.cd_bairro LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND bairros.nome LIKE '%$pesquisaDescricao%'";
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

            $dadosInsert = ["CD_BAIRRO" => $this->codigo, "NOME" => $this->nome];
            $insert->ExeInsert("BAIRROS", $dadosInsert);

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

            $read->ExeRead("BAIRROS", "WHERE CD_BAIRRO = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $conn = \App\Conn\Conn::getConn();
                $update = new \App\Conn\Update($conn);

                $dadosUpdate = ["CD_BAIRRO" => $this->codigo, "NOME" => $this->nome];

                $update->ExeUpdate("BAIRROS", $dadosUpdate, "WHERE CD_BAIRRO = :D", "D=$this->codigo");

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

            $delete->ExeDelete("BAIRROS", "WHERE CD_BAIRRO = :C", "C=$this->codigo");

            $delete->Commit();
            $this->Result = true;
        } catch (Exception $e) {
            $this->Message = $e->getMessage();
            $delete->Rollback();
            $this->Result = false;
        }
    }

    public function generalSearch($arrayParam){
        try{
            $colunas = $arrayParam['colunas'];
            $descricao = !empty($arrayParam['descricaoPesquisa']) ? $arrayParam['descricaoPesquisa'] : '';
            
            $read = new \App\Conn\Read();

            $query = "SELECT $colunas FROM BAIRROS WHERE 1=1";

            if(!empty($descricao)){
                $query .= " AND bairros.nome LIKE '%$descricao%'";
            }

            $query .= " LIMIT 30";

            $read->FullRead($query);
            $this->Result = true;
            $this->Return = $read->getResult();
        } catch(Exception $e){
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
    public function getNome()
    {
        return $this->nome;
    }
}
