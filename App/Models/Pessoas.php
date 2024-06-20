<?php

namespace App\Models;

use Exception;

class Pessoas
{

    private $CdPessoa;
    private $NmPessoa;
    private \App\Models\Municipios $cidade;
    private \App\Models\Bairros $bairro;
    private \App\Models\Logradouros $logradouro;
    private $NrTelefone;
    private $NrCelular;
    private $DsEmail;
    private $NrCRMV;
    private $ativo;

    private $Return;
    private $Result;
    private $Message;


    public function __construct($nmpessoa, $cdCidade, $nrtelefone, $nrCelular, $dsEmail, $nrCrmv, $cdBairro, $cdLogradouro, $ativo, $cdpessoa = null)
    {
        $this->CdPessoa = $cdpessoa;
        $this->NmPessoa = $nmpessoa;
        $this->NrTelefone = $nrtelefone;
        $this->NrCelular = $nrCelular;
        $this->DsEmail = $dsEmail;
        $this->NrCRMV = $nrCrmv;
        $this->ativo = $ativo;
        $this->cidade = \App\Models\Municipios::findById($cdCidade);
        $this->bairro = \App\Models\Bairros::findById($cdBairro);
        $this->logradouro = \App\Models\Logradouros::findById($cdLogradouro);
    }

    // public function verificarAcesso()
    // {
    //     $read = new \App\Conn\Read();

    //     $read->FullRead("SELECT * FROM USUARIOS WHERE USUARIOS.USUARIO =:L AND USUARIOS.SENHA = :S LIMIT 1", "L=$this->login&S=$this->senha");

    //     if ($read->getRowCount() == 0) {
    //         return null;
    //     }
    //     return $read->getResult();
    // }


    public static function findById($id)
    {
        try {
            if (empty($id)) {
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read();

            $read->ExeRead("PESSOAS", "WHERE CD_PESSOA = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self(
                $read->getResult()[0]['nm_pessoa'],
                $read->getResult()[0]['cd_cidade'],
                $read->getResult()[0]['nr_telefone'],
                '',
                $read->getResult()[0]['ds_email'],
                $read->getResult()[0]['nr_crmv'],
                $read->getResult()[0]['cd_bairro'],
                $read->getResult()[0]['cd_logradouro'],
                $read->getResult()[0]['fl_ativo'],
                $read->getResult()[0]['cd_pessoa']
            );
        } catch (Exception $e) {
            return new self('', '', '', '', '', '', '', '', '', '');
        }
    }


    public function Insert()
    {
        $insert = new \App\Conn\Insert();

        try {
            $insert->ExeInsert("PESSOAS", [
                "NM_PESSOA" => $this->NmPessoa,
                "CD_CIDADE" => $this->cidade->getCodigo(),
                "CD_BAIRRO" => $this->bairro->getCodigo(),
                "CD_LOGRADOURO" => $this->logradouro->getCodigo(),
                "NR_TELEFONE" => $this->NrTelefone,
                "NR_CELULAR" => $this->NrCelular,
                "DS_EMAIL" => $this->DsEmail,
                "NR_CRMV" => $this->NrCRMV,
                "FL_ATIVO" => $this->ativo
            ]);

            if (!$insert->getResult()) {
                throw new Exception($insert->getMessage());
            }
            $this->Result = true;
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function Update()
    {
        $read = new \App\Conn\Read();
        try {
            $read->ExeRead("PESSOAS", "WHERE CD_PESSOA = :C", "C=$this->CdPessoa");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {
                $dadosUpdate = [
                    "NM_PESSOA" => $this->NmPessoa,
                    "CD_CIDADE" => $this->cidade->getCodigo(),
                    "CD_BAIRRO" => $this->bairro->getCodigo(),
                    "CD_LOGRADOURO" => $this->logradouro->getCodigo(),
                    "NR_TELEFONE" => $this->NrTelefone,
                    "NR_CELULAR" => $this->NrCelular,
                    "DS_EMAIL" => $this->DsEmail,
                    "NR_CRMV" => $this->NrCRMV,
                    "FL_ATIVO" => $this->ativo
                ];

                $update = new \App\Conn\Update();

                $update->ExeUpdate("PESSOAS", $dadosUpdate, "WHERE CD_PESSOA = :C", "C=$this->CdPessoa");

                if (!$update->getResult()) {
                    throw new Exception($update->getMessage());
                }
                $this->Result = true;
            } else {
                throw new Exception("Ops Parece que esse registro não existe mais na base de dados");
            }
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public static function GeneralSearch($search)
    {
        $read = new \App\Conn\Read();
        if (!empty($search)) {
            $read->FullRead("SELECT P.* FROM pessoas P  WHERE UPPER(CONCAT(P.CD_PESSOA, ' ', P.NM_PESSOA)) LIKE UPPER(CONCAT('%', :P, '%')) ORDER BY P.NM_PESSOA ASC", "P=$search");
        } else {
            $read->FullRead("SELECT P.* FROM PESSOAS P");
        }
        return $read->getResult();
    }

    public static function RetornaDadosPessoa($cdPessoa)
    {
        $read = new \App\Conn\Read();
        $read->FullRead("SELECT P.* FROM pessoas P  WHERE P.CD_PESSOA = :C", "C=$cdPessoa");

        return $read->getResult()[0];
    }

    public function Delete()
    {
        try {
            $delete = new \App\Conn\delete();

            $delete->ExeDelete("pessoas", "WHERE CD_PESSOA =:C", "C=$this->CdPessoa");

            if (!$delete->getResult()[0]) throw new Exception($delete->getResult()[1]);

            $delete->Commit();
            $this->Result = true;
        } catch (Exception $e) {
            $delete->Rollback();
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public static function AtualizarExclusaoPessoa($cdPessoa, $acao)
    {
        $read = new \App\Conn\Read();
        $read->ExeRead("pessoas", "WHERE CD_PESSOA = :C", "C=$cdPessoa");
        $dadosFicha = $read->getResult()[0] ?? [];
        if ($dadosFicha) {
            $update = new \App\Conn\Update();

            if ($acao == 'excluir') {
                $update->ExeUpdate("pessoas", ['FL_EXCLUIDO' => 'S'], "WHERE CD_PESSOA = :C", "C=$cdPessoa");
            } else {
                $update->ExeUpdate("pessoas", ['FL_EXCLUIDO' => 'N'], "WHERE CD_PESSOA = :C", "C=$cdPessoa");
            }

            $atualizado = !empty($update->getResult());

            if ($atualizado) {
                return true;
            } else {
                return false;
            }
        } else {
            return "Ops! PARECE QUE ESSE REGISTRO NÃO EXISTE NA BASE DE DADOS!";
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

        $query = "SELECT pessoas.cd_pessoa,
                  pessoas.nm_pessoa,
                  (CASE WHEN pessoas.fl_ativo = 'S' THEN 'Sim' ELSE 'Não' END) as fl_ativo, 
                  COUNT(pessoas.CD_PESSOA) OVER() AS total_filtered,  
                  (SELECT COUNT(pessoas.CD_PESSOA) FROM pessoas) AS total_table 
                  FROM pessoas
                  WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND pessoas.cd_pessoa LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND pessoas.nm_pessoa LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaAtivo)) {
            $query .= " AND pessoas.fl_ativo LIKE '%$pesquisaAtivo%'";
        }

        if (!empty($orderBy)) {
            $query .= " ORDER BY $orderBy $orderAscDesc";
        }

        $query .= " LIMIT $start, $limit";

        $read->FullRead($query);

        return $read->getResult();
    }


    public function getCodigo()
    {
        return $this->CdPessoa;
    }

    public function getNome()
    {
        return $this->NmPessoa;
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function getBairro()
    {
        return $this->bairro;
    }

    public function getLogradouro()
    {
        return $this->logradouro;
    }

    public function getTelefone()
    {
        return $this->NrTelefone;
    }

    public function getCelular()
    {
        return $this->NrCelular;
    }

    public function getEmail()
    {
        return $this->DsEmail;
    }

    public function getNrCRMV()
    {
        return $this->NrCRMV;
    }

    public function getAtivo()
    {
        return $this->ativo;
    }

    public function getReturn()
    {
        return $this->Return;
    }

    public function getResult()
    {
        return $this->Result;
    }

    public function getMessage()
    {
        return $this->Message;
    }
}
