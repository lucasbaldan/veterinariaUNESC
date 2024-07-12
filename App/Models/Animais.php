<?php

namespace App\Models;

use Exception;

class Animais
{

    private $codigo;
    private $nome;
    private $fl_tutor_nao_declarado;
    private \App\Models\Pessoas $tutor1;
    private \App\Models\Raças $raca;
    private \App\Models\Especies $especie;
    private $sexo;

    private $Result;
    private $Message;
    private $Return;

    public function __construct($nome, $tutorDeclarado, $cdEspecie, $cdRaca, $sexo, $cdTutor1, $codigo = null)
    {
        $this->nome = $nome;
        $this->fl_tutor_nao_declarado = $tutorDeclarado;
        $this->especie = \App\Models\Especies::findById($cdEspecie);
        $this->raca = \App\Models\Raças::findById($cdRaca);
        $this->sexo = $sexo;
        $this->tutor1 = \App\Models\Pessoas::findById($cdTutor1);
        $this->codigo = $codigo;
    }

    public static function findById($id, $Conn = false)
    {
        try {
            if (empty($id)) {
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read($Conn);

            $read->ExeRead("ANIMAIS", "WHERE CD_ANIMAL = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self(
                $read->getResult()[0]['NM_ANIMAL'],
                $read->getResult()[0]['FL_TUTOR_NAO_DECLARADO'],
                $read->getResult()[0]['CD_ESPECIE'],
                $read->getResult()[0]['CD_RACA'],
                $read->getResult()[0]['SEXO'],
                $read->getResult()[0]['CD_PESSOA_TUTOR1'],
                $read->getResult()[0]['CD_ANIMAL']
            );
        } catch (Exception $e) {
            return new self('', '', '', '', '', '', '');
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
        $pesquisaEspecie = $arrayParam['pesquisaEspecie'];
        $pesquisaRaca = $arrayParam['pesquisaRaca'];
        $pesquisaDono = $arrayParam['pesquisaDono'];

        $read = new \App\Conn\Read();

        $query = "SELECT ANIMAIS.CD_ANIMAL,
                ANIMAIS.NM_ANIMAL,
                PESSOAS.NM_PESSOA AS NOME_DONO,
                ESPECIES.DESCRICAO AS ESPECIE_DESCRICAO,
                RACAS.DESCRICAO AS RACA_DESCRICAO,
                (SELECT COUNT(*) 
                    FROM ANIMAIS 
                    LEFT JOIN PESSOAS ON (ANIMAIS.CD_PESSOA_TUTOR1 = PESSOAS.CD_PESSOA)
                    LEFT JOIN RACAS ON (ANIMAIS.CD_RACA = RACAS.CD_RACA)
                    LEFT JOIN ESPECIES ON (RACAS.CD_ESPECIE = ESPECIES.CD_ESPECIE)
                    WHERE 1=1) AS TOTAL_FILTERED,
                (SELECT COUNT(*) FROM ANIMAIS) AS TOTAL_TABLE 
            FROM ANIMAIS
            LEFT JOIN PESSOAS ON (ANIMAIS.CD_PESSOA_TUTOR1 = PESSOAS.CD_PESSOA) 
            LEFT JOIN RACAS ON (ANIMAIS.CD_RACA = RACAS.CD_RACA)
            LEFT JOIN ESPECIES ON (RACAS.CD_ESPECIE = ESPECIES.CD_ESPECIE)
            WHERE 1=1";
        // $query = "SELECT animais.cd_animal,
        //           animais.nm_animal,
        //           tipo_animal.descricao as tipo_animal_descricao,
        //           '-' as nome_dono,
        //           especies.descricao especie_descricao,
        //           racas.descricao raca_descricao,
        //           COUNT(animais.cd_animal) OVER() AS total_filtered,  
        //           (SELECT COUNT(animais.cd_animal) FROM animais) AS total_table 
        //           FROM animais
        //           INNER JOIN tipo_animal ON (animais.cd_tipo_animal = tipo_animal.cd_tipo_animal)
        //           LEFT JOIN racas ON (animais.cd_raca = racas.cd_raca)
        //           LEFT JOIN especies ON (racas.cd_especie = especies.cd_especie)
        //           WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND ANIMAIS.CD_ANIMAL LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND ANIMAIS.NM_ANIMAL LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaEspecie)) {
            $query .= " AND ESPECIES.DESCRICAO LIKE '%$pesquisaEspecie%'";
        }
        if (!empty($pesquisaRaca)) {
            $query .= " AND RACAS.DESCRICAO LIKE '%$pesquisaRaca%'";
        }
        if (!empty($pesquisaDono)) {
            $query .= " AND PESSOAS.NM_PESSOA LIKE '%$pesquisaDono%'";
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
            $conn = \App\Conn\Conn::getConn(true);
            $insert = new \App\Conn\Insert($conn);

            $dadosInsert = [
                "NM_ANIMAL" => $this->nome,
                "FL_TUTOR_NAO_DECLARADO" => $this->fl_tutor_nao_declarado,
                "CD_PESSOA_TUTOR1" => $this->tutor1->getCodigo(),
                "CD_ESPECIE" => $this->especie->getCodigo(),
                "CD_RACA" => $this->raca->getCodigo(),
                "SEXO" => $this->sexo,
            ];

            $insert->ExeInsert("ANIMAIS", $dadosInsert);

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

    public function Atualizar($Conn = false)
    {
        try {
            $read = new \App\Conn\Read($Conn);

            $read->ExeRead("ANIMAIS", "WHERE CD_ANIMAL = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $update = new \App\Conn\Update($Conn);

                $dadosUpdate = [
                    "NM_ANIMAL" => $this->nome,
                    "FL_tutor_NAO_DECLARADO" => $this->fl_tutor_nao_declarado,
                    "CD_PESSOA_TUTOR1" => $this->tutor1->getCodigo(),
                    "CD_ESPECIE" => $this->especie->getCodigo(),
                    "CD_RACA" => $this->raca->getCodigo(),
                    "SEXO" => $this->sexo,
                ];

                $update->ExeUpdate("ANIMAIS", $dadosUpdate, "WHERE CD_ANIMAL = :D", "D=$this->codigo");

                if (!$update->getResult()) {
                    throw new Exception($update->getMessage());
                }
                $this->Result = true;
            } else {
                throw new Exception("Ops, Parece que esse registro não existe mais na base de dados!");
            }
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function Excluir()
    {

        try {
            $conn = \App\Conn\Conn::getConn();
            $delete = new \App\Conn\Delete($conn);

            $delete->ExeDelete("ANIMAIS", "WHERE CD_ANIMAL = :C", "C=$this->codigo");

            $delete->Commit();
            $this->Result = true;
        } catch (Exception $e) {
            $this->Message = $e->getMessage();
            $delete->Rollback();
            $this->Result = false;
        }
    }

    public static function GeneralSearch($search)
    {
        try {

            $colunas = !empty($search['COLUNAS']) ? $search['COLUNAS'] : '*';
            $nome = !empty($search['NM_ANIMAL']) ? $search['NM_ANIMAL'] : '';
            $tutor1 = !empty($search['DONO']) ? $search['DONO'] : '';

            $read = new \App\Conn\Read();

            $query = "SELECT $colunas
          FROM ANIMAIS
          LEFT JOIN PESSOAS DONO ON (ANIMAIS.CD_PESSOA_TUTOR1 = DONO.CD_PESSOA)
          WHERE 1=1 ";

            if (!empty($nome)) $query .= " AND ANIMAIS.NM_ANIMAL LIKE '%$nome%' ";
            if (!empty($tutor1)) $query .= " AND DONO.NM_PESSOA LIKE '%$tutor1%' ";

            $query .= "LIMIT 50";

            // if (!empty($search)) {
            //     $read->FullRead("SELECT P.* FROM pessoas P  WHERE UPPER(CONCAT(P.CD_PESSOA, ' ', P.NM_PESSOA)) LIKE UPPER(CONCAT('%', :P, '%')) ORDER BY P.NM_PESSOA ASC", "P=$search");
            // } else {
            //     $read->FullRead("SELECT P.* FROM PESSOAS P");
            // }

            $read->FullRead($query);

            if ($read->getRowCount() == 0) return null;
            else return $read->getResult();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // public function generalSearch($arrayParam){
    //     try{
    //         $colunas = $arrayParam['colunas'];
    //         $descricao = !empty($arrayParam['descricaoPesquisa']) ? $arrayParam['descricaoPesquisa'] : '';

    //         $read = new \App\Conn\Read();

    //         $query = "SELECT $colunas FROM ESPECIES WHERE 1=1";

    //         if(!empty($descricao)){
    //             $query .= " AND DESCRICAO LIKE '%$descricao%'";
    //         }

    //         $read->FullRead($query);
    //         $this->Result = true;
    //         $this->Return = $read->getResult();
    //     } catch(Exception $e){
    //         $this->Result = false;
    //         $this->Message = $e->getMessage();

    //     }
    // }


    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getFlDonoNaoDeclarado()
    {
        return $this->fl_tutor_nao_declarado;
    }

    public function getDono1()
    {
        return $this->tutor1;
    }

    public function getRaca()
    {
        return $this->raca;
    }

    public function getEspecie()
    {
        return $this->especie;
    }

    public function getSexo()
    {
        return $this->sexo;
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

    public function setDonoNaoDeclarado($fl)
    {
        $this->fl_tutor_nao_declarado = $fl;
    }

    public function setDono1($tutor1)
    {
        $this->tutor1 = \App\Models\Pessoas::findById($tutor1);
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function setEspecie($cd)
    {
        $this->especie = \App\Models\Especies::findById($cd);
    }
    public function setRaca($cd)
    {
        $this->raca = \App\Models\Raças::findById($cd);
    }
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }
}
