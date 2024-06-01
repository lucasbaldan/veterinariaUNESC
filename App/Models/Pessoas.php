<?php

namespace App\Models;

use Exception;

class Pessoas
{

    private $CdPessoa;
    private $login;
    private $senha;
    private $NmPessoa;
    private $DsCidade;
    private $NrTelefone;
    private $DsEmail;
    private $NrCRMV;
    private $Return;
    private $Result;
    private $Message;


    public function __construct($login, $senha, $nmpessoa = null, $dscidade = null, $nrtelefone = null, $dsemail = null, $nrcrmv = null, $cdpessoa = null)
    {
        $this->login = $login;
        $this->senha = $senha;
        $this->CdPessoa = $cdpessoa;

        $this->NmPessoa = $nmpessoa;
        $this->DsCidade = $dscidade;
        $this->NrTelefone = $nrtelefone;
        $this->DsEmail = $dsemail;
        $this->NrCRMV = $nrcrmv;
    }

    public function verificarAcesso()
    {
        $read = new \App\Conn\Read();

        $read->FullRead("SELECT * FROM USUARIOS WHERE USUARIOS.USUARIO =:L AND USUARIOS.SENHA = :S LIMIT 1", "L=$this->login&S=$this->senha");

        if ($read->getRowCount() == 0) {
            return null;
        }
        return $read->getResult();
    }

    public function Insert()
    {
        $insert = new \App\Conn\Insert();

        try {
            $insert->ExeInsert("pessoas", [
                // "CD_PESSOA" => $this->CdPessoa,
                "NM_PESSOA" => $this->NmPessoa,
                "CIDADE" => $this->DsCidade,
                "NR_TELEFONE" => $this->NrTelefone,
                "DS_EMAIL" => $this->DsEmail,
                "NR_CRMV" => $this->NrCRMV,
            ]);

            if (!$insert->getResult()) {
                throw new Exception($insert->getError());
            }
            $this->Result = true;
            $this->Return = $insert->getLastInsert();
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function Update()
    {
        $read = new \App\Conn\Read();
        try {
            $read->ExeRead("pessoas", "WHERE CD_PESSOA = :C", "C=$this->CdPessoa");
            $dadosFicha = $read->getResult()[0] ?? [];
            if ($dadosFicha) {
                $dadosUpdate = [
                    // "CD_PESSOA" => $this->CdPessoa,
                    "NM_PESSOA" => $this->NmPessoa,
                    "CIDADE" => $this->DsCidade,
                    "NR_TELEFONE" => $this->NrTelefone,
                    "DS_EMAIL" => $this->DsEmail,
                    "NR_CRMV" => $this->NrCRMV,
                ];

                $update = new \App\Conn\Update();

                $update->ExeUpdate("pessoas", $dadosUpdate, "WHERE CD_PESSOA = :C", "C=$this->CdPessoa");
                $atualizado = !empty($update->getResult());

                if (!$atualizado) {
                    throw new Exception($update->getError());
                }
                $this->Result = true;
            } else {
                throw new Exception("Ops! PARECE QUE ESSE REGISTRO NÃO EXISTE NA BASE DE DADOS!");
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
        $read->FullRead("SELECT P.* FROM pessoas P  WHERE F.CD_PESSOA = :C", "C=$cdPessoa");

        return $read->getResult();
    }

    public static function Delete($cdPessoa)
    {
        $delete = new \App\Conn\delete();

        $delete->ExeDelete("pessoas", "WHERE CD_PESSOA =:C", "C=$cdPessoa");
        $deletado = !empty($delete->getResult());

        if ($deletado) {
            return true;
        } else {
            return false;
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

    public function GetMessage()
    {
        return $this->Message;
    }

    public function GetResult()
    {
        return $this->Result;
    }

    public function GetReturn()
    {
        return $this->Return;
    }
}
