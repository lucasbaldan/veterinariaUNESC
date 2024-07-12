<?php

namespace App\Models;

use Exception;

class Pessoas
{

    private $CdPessoa;
    private $NmPessoa;
    private $cpf;
    private $dataNascimento;
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


    public function __construct($nmpessoa, $cdCidade, $nrtelefone, $nrCelular, $dsEmail, $nrCrmv, $cdBairro, $cdLogradouro, $ativo, $cpf, $dataNascimento, $cdpessoa = null)
    {
        $this->CdPessoa = $cdpessoa;
        $this->NmPessoa = $nmpessoa;
        $this->cpf = $cpf;
        $this->dataNascimento = $dataNascimento;
        $this->NrTelefone = $nrtelefone;
        $this->NrCelular = $nrCelular;
        $this->DsEmail = $dsEmail;
        $this->NrCRMV = $nrCrmv;
        $this->ativo = $ativo;
        $this->cidade = \App\Models\Municipios::findById($cdCidade);
        $this->bairro = \App\Models\Bairros::findById($cdBairro);
        $this->logradouro = \App\Models\Logradouros::findById($cdLogradouro);

        $this->Result = true;
        $this->Message = '';
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


    public static function findById($id, $Conn = false)
    {
        try {
            if (empty($id)) {
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read($Conn);

            $read->ExeRead("PESSOAS", "WHERE CD_PESSOA = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self(
                $read->getResult()[0]['NM_PESSOA'],
                $read->getResult()[0]['CD_CIDADE'],
                $read->getResult()[0]['NR_TELEFONE'],
                '',
                $read->getResult()[0]['DS_EMAIL'],
                $read->getResult()[0]['NR_CRMV'],
                $read->getResult()[0]['CD_BAIRRO'],
                $read->getResult()[0]['CD_LOGRADOURO'],
                $read->getResult()[0]['FL_ATIVO'],
                $read->getResult()[0]['CPF'],
                $read->getResult()[0]['DATA_NASCIMENTO'],
                $read->getResult()[0]['CD_PESSOA']
            );
        } catch (Exception $e) {
            return new self('', '', '', '', '', '', '', '', '', '', '');
        }
    }


    public function Insert($Conn = false)
    {
        $insert = new \App\Conn\Insert($Conn);

        try {
            $insert->ExeInsert("PESSOAS", [
                "NM_PESSOA" => $this->NmPessoa,
                "CPF" => $this->cpf,
                "DATA_NASCIMENTO" => $this->dataNascimento,
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
            $this->CdPessoa = $insert->getLastInsert();
            $this->Result = true;
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function Update($Conn = false)
    {
        $read = new \App\Conn\Read($Conn);
        try {
            $read->ExeRead("PESSOAS", "WHERE CD_PESSOA = :C", "C=$this->CdPessoa");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {
                $dadosUpdate = [
                    "NM_PESSOA" => $this->NmPessoa,
                    "CPF" => $this->cpf,
                    "DATA_NASCIMENTO" => $this->dataNascimento,
                    "CD_CIDADE" => $this->cidade->getCodigo(),
                    "CD_BAIRRO" => $this->bairro->getCodigo(),
                    "CD_LOGRADOURO" => $this->logradouro->getCodigo(),
                    "NR_TELEFONE" => $this->NrTelefone,
                    "NR_CELULAR" => $this->NrCelular,
                    "DS_EMAIL" => $this->DsEmail,
                    "NR_CRMV" => $this->NrCRMV,
                    "FL_ATIVO" => $this->ativo
                ];

                $update = new \App\Conn\Update($Conn);

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

    public function GeneralSearch($search)
    {

        $colunas = !empty($search['COLUNAS']) ? $search['COLUNAS'] : '*';
        $nome = !empty($search['NM_PESSOA']) ? $search['NM_PESSOA'] : '';
        $cidade = !empty($search['ID_CIDADE']) ? $search['ID_CIDADE'] : '';
        $telefone = !empty($search['TELEFONE']) ? $search['TELEFONE'] : '';

        $read = new \App\Conn\Read();

        $query = "SELECT $colunas
          FROM PESSOAS
          LEFT JOIN CIDADES ON (PESSOAS.CD_CIDADE = CIDADES.CD_CIDADE)
          WHERE PESSOAS.FL_ATIVO = 'S' ";

        if (!empty($nome)) {
            $query .= " AND PESSOAS.NM_PESSOA LIKE '%$nome%' ";
        }
        if (!empty($cidade)) {
            $query .= " AND CIDADES.CD_CIDADE = $cidade ";
        }
        if (!empty($telefone)) {
            $query .= " AND PESSOAS.NR_TELEFONE LIKE  '%$telefone%' ";
        }

        $query .= "LIMIT 100";

        $read->FullRead($query);
        if (empty($read->getResult())) {
            return false;
        } else {
            $this->Result = true;
            $this->Return = $read->getResult();
            return $read->getResult();
        }

        // if ($read->getRowCount() == 0) {
        //     return null;
        // } else {
        //     return $read->getResult();
        // }
    }

    public static function RetornaDadosPessoa($cdPessoa)
    {
        $read = new \App\Conn\Read();
        $read->FullRead("SELECT P.* FROM PESSOAS
                          WHERE CD_PESSOA = :C", "C=$cdPessoa");

        return $read->getResult();
    }

    public function Delete()
    {
        try {
            $delete = new \App\Conn\delete();

            $delete->ExeDelete("PESSOAS", "WHERE CD_PESSOA =:C", "C=$this->CdPessoa");

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
        $read->ExeRead("PESSOAS", "WHERE CD_PESSOA = :C", "C=$cdPessoa");
        $dadosFicha = $read->getResult()[0] ?? [];
        if ($dadosFicha) {
            $update = new \App\Conn\Update();

            if ($acao == 'excluir') {
                $update->ExeUpdate("pessoas", ['FL_EXCLUIDO' => 'S'], "WHERE CD_PESSOA = :C", "C=$cdPessoa");
            } else {
                $update->ExeUpdate("PESSOAS", ['FL_EXCLUIDO' => 'N'], "WHERE CD_PESSOA = :C", "C=$cdPessoa");
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

        $query = "SELECT 
                    PESSOAS.CD_PESSOA,
                    PESSOAS.NM_PESSOA,
                    (CASE WHEN PESSOAS.FL_ATIVO = 'S' THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO, 
                    (SELECT COUNT(*) FROM PESSOAS WHERE 1=1) AS TOTAL_FILTERED,  
                    (SELECT COUNT(*) FROM PESSOAS) AS TOTAL_TABLE 
                    FROM 
                        PESSOAS
                    WHERE 
                        1=1;
                    ";

        // $query = "SELECT PESSOAS.CD_PESSOA,
        //           PESSOAS.NM_PESSOA,
        //           (CASE WHEN PESSOAS.FL_ATIVO = 'S' THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO, 
        //           COUNT(PESSOAS.CD_PESSOA) OVER() AS TOTAL_FILTERED,  
        //           (SELECT COUNT(PESSOAS.CD_PESSOA) FROM PESSOAS) AS TOTAL_TABLE 
        //           FROM PESSOAS
        //           WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND PESSOAS.CD_PESSOA LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND PESSOAS.NM_PESSOA LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaAtivo)) {
            $query .= " AND PESSOAS.FL_ATIVO LIKE '%$pesquisaAtivo%'";
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
    public function getCPF()
    {
        return $this->cpf;
    }
    public function getDataNascimento()
    {
        return $this->dataNascimento;
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

    public function setNome($nome)
    {
        $this->NmPessoa = $nome;
    }

    public function setTelefone($telefone)
    {
        $this->NrTelefone = $telefone;
    }

    public function setCRMV($crmv)
    {
        $this->NrCRMV = $crmv;
    }

    public function setEmail($email)
    {
        $this->DsEmail = $email;
    }

    public function setCidade($cidade)
    {
        $this->cidade = \App\Models\Municipios::findById($cidade);
    }

    public function setBairro($bairro)
    {
        $this->bairro = \App\Models\Bairros::findById($bairro);
    }

    public function setLogradouro($logradouro)
    {
        $this->logradouro = \App\Models\Logradouros::findById($logradouro);
    }
}
