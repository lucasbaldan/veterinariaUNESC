<?php

namespace App\Models;

use Exception;

class Animais
{

    private $codigo;
    private $nome;
    private $fl_dono_nao_declarado;
    private \App\Models\Pessoas $dono1;
    private $dono2;
    private \App\Models\Raças $raca;
    private \App\Models\Especies $especie;
    private \App\Models\TipoAnimais $tipoAnimal;
    private $sexo;
    private $idadeAproximada;
    private $anoNascimento;

    private $Result;
    private $Message;
    private $Return;

    public function __construct($nome, $donoDeclarado, $cdTipoAnimal, $cdEspecie, $cdRaca, $sexo, $idadeAproximada, $anoNascimento, $cdDono1, $cdDono2 = null , $codigo = null)
    {
        $this->nome = $nome;
        $this->fl_dono_nao_declarado = $donoDeclarado;
        $this->dono1 = \App\Models\Pessoas::findById($cdDono1);
        // dono 2;
        $this->raca = \App\Models\Raças::findById($cdRaca);
        $this->tipoAnimal = \App\Models\TipoAnimais::findById($cdTipoAnimal);
        $this->especie = \App\Models\Especies::findById($cdEspecie);
        $this->sexo = $sexo;
        $this->idadeAproximada = $idadeAproximada;
        $this->anoNascimento = $anoNascimento;
        $this->codigo = $codigo;
    }

    public static function findById($id)
    {
        try {
            if(empty($id)){
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read();

            $read->ExeRead("ANIMAIS", "WHERE CD_ANIMAL = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self($read->getResult()[0]['nm_animal'], 
                            $read->getResult()[0]['fl_dono_nao_declarado'], 
                            $read->getResult()[0]['cd_tipo_animal'], 
                            $read->getResult()[0]['cd_especie'], 
                            $read->getResult()[0]['cd_raca'],
                            $read->getResult()[0]['sexo'],
                            $read->getResult()[0]['idade_aproximada'],
                            $read->getResult()[0]['ano_nascimento'], 
                            $read->getResult()[0]['cd_pessoa_dono1'], 
                            $read->getResult()[0]['cd_pessoa_dono2'], 
                            $read->getResult()[0]['cd_animal']);

        } catch (Exception $e) {
            return new self('','','','','','','','','','');
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
        $pesquisaTipoAnimal = $arrayParam['pesquisaTipoAnimal'];
        $pesquisaEspecie = $arrayParam['pesquisaEspecie'];
        $pesquisaRaca = $arrayParam['pesquisaRaca'];
        $pesquisaDono = $arrayParam['pesquisaDono'];

        $read = new \App\Conn\Read();

        $query = "SELECT animais.cd_animal,
                  animais.nm_animal,
                  tipo_animal.descricao as tipo_animal_descricao,
                  '-' as nome_dono,
                  especies.descricao especie_descricao,
                  racas.descricao raca_descricao,
                  COUNT(animais.cd_animal) OVER() AS total_filtered,  
                  (SELECT COUNT(animais.cd_animal) FROM animais) AS total_table 
                  FROM animais
                  INNER JOIN tipo_animal ON (animais.cd_tipo_animal = tipo_animal.cd_tipo_animal)
                  LEFT JOIN racas ON (animais.cd_raca = racas.cd_raca)
                  LEFT JOIN especies ON (racas.cd_especie = especies.cd_especie)
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND animais.cd_animal LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND animais.nm_animal LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaTipoAnimal)) {
            $query .= " AND tipo_animal.descricao LIKE '%$pesquisaTipoAnimal%'";
        }
        if (!empty($pesquisaEspecie)) {
            $query .= " AND especies.descricao LIKE '%$pesquisaEspecie%'";
        }
        if (!empty($pesquisaRaca)) {
            $query .= " AND racas.descricao LIKE '%$pesquisaRaca%'";
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

            $dadosInsert = ["NM_ANIMAL" => $this->nome, 
                            "FL_DONO_NAO_DECLARADO" => $this->fl_dono_nao_declarado, 
                            "CD_PESSOA_DONO1" => $this->dono1->getCodigo(),
                            "CD_PESSOA_DONO2" => null,
                            "CD_ESPECIE" => $this->especie->getCodigo(),
                            "CD_RACA" => $this->raca->getCodigo(),
                            "SEXO" => $this->sexo,
                            "IDADE_APROXIMADA" => $this->idadeAproximada,
                            "ANO_NASCIMENTO" => $this->anoNascimento,
                            "CD_TIPO_ANIMAL" => $this->tipoAnimal->getCodigo()];
            
             $insert->ExeInsert("ANIMAIS", $dadosInsert);

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

            $read->ExeRead("ANIMAIS", "WHERE CD_ANIMAL = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $conn = \App\Conn\Conn::getConn(true);
                $update = new \App\Conn\Update($conn);

               $dadosUpdate = [
                "NM_ANIMAL" => $this->nome, 
                "FL_DONO_NAO_DECLARADO" => $this->fl_dono_nao_declarado, 
                "CD_PESSOA_DONO1" => $this->dono1->getCodigo(),
                "CD_PESSOA_DONO2" => null,
                "CD_ESPECIE" => $this->especie->getCodigo(),
                "CD_RACA" => $this->raca->getCodigo(),
                "SEXO" => $this->sexo,
                "IDADE_APROXIMADA" => $this->idadeAproximada,
                "ANO_NASCIMENTO" => $this->anoNascimento,
                "CD_TIPO_ANIMAL" => $this->tipoAnimal->getCodigo()];

                $update->ExeUpdate("ANIMAIS", $dadosUpdate, "WHERE CD_ANIMAL = :D", "D=$this->codigo");

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

            $delete->ExeDelete("ANIMAIS", "WHERE CD_ANIMAL = :C", "C=$this->codigo");

            $delete->Commit();
            $this->Result = true;
        } catch (Exception $e) {
            $this->Message = $e->getMessage();
            $delete->Rollback();
            $this->Result = false;
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
        return $this->fl_dono_nao_declarado;
    }

    public function getDono1()
    {
        return $this->dono1;
    }

    public function getDono2()
    {
        return $this->dono2;
    }

    public function getRaca()
    {
        return $this->raca;
    }

    public function getEspecie()
    {
        return $this->especie;
    }

    public function getTipoAnimal()
    {
        return $this->tipoAnimal;
    }

    public function getSexo()
    {
        return $this->sexo;
    }

    public function getIdadeAproximada()
    {
        return $this->idadeAproximada;
    }

    public function getAnoNascimento()
    {
        return $this->anoNascimento;
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
}
