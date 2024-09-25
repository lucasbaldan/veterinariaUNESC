<?php

namespace App\Models;

use Exception;

class Laboratorios
{

    private $CdLaboratorio;
    private $NmLaboratorio;
    private $ativo;

    private $idLogo;

    private $Return;
    private $Result;
    private $Message;


    public function __construct($nmlaboratorio, $ativo, $cdlaboratorio = null)
    {
        $this->CdLaboratorio = $cdlaboratorio;
        $this->NmLaboratorio = $nmlaboratorio;
        $this->ativo = $ativo;

        $this->Result = true;
        $this->Message = '';
    }


    public static function findById($id, $Conn = false)
    {
        try {
            if (empty($id)) {
                throw new Exception("Objeto vazio");
            }

            $read = new \App\Conn\Read($Conn);

            $read->ExeRead("LABORATORIOS", "WHERE CD_LABORATORIO = :C LIMIT 1", "C=$id");

            if ($read->getRowCount() == 0) {
                throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
            }

            return new self(
                $read->getResult()[0]['NM_LABORATORIO'],
                $read->getResult()[0]['FL_ATIVO'],
                $read->getResult()[0]['CD_LABORATORIO'],
            );
        } catch (Exception $e) {
            return new self('', '', '');
        }
    }


    public function Insert($Conn = false)
    {
        $insert = new \App\Conn\Insert($Conn);

        try {
            $insert->ExeInsert("LABORATORIOS", [
                "NM_LABORATORIO" => $this->NmLaboratorio,
                "FL_ATIVO" => $this->ativo,
            ]);

            if (!$insert->getResult()) {
                throw new Exception($insert->getMessage());
            }
            $this->CdLaboratorio = $insert->getLastInsert();

            $logs = new \App\Models\Logs($_SESSION['username'], 'INSERT', 'LABORATORIOS', $this->CdLaboratorio, [
                "NM_LABORATORIO" => $this->NmLaboratorio,
                "FL_ATIVO" => $this->ativo
            ]);
            $logs->Insert();

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
            $read->ExeRead("LABORATORIOS", "WHERE CD_LABORATORIO = :C", "C=$this->CdLaboratorio");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {
                $dadosUpdate = [
                    "NM_LABORATORIO" => $this->NmLaboratorio,
                    "FL_ATIVO" => $this->ativo
                ];

                $update = new \App\Conn\Update($Conn);

                $update->ExeUpdate("LABORATORIOS", $dadosUpdate, "WHERE CD_LABORATORIO = :C", "C=$this->CdLaboratorio");

                if (!$update->getResult()) {
                    throw new Exception($update->getMessage());
                }

                $logs = new \App\Models\Logs($_SESSION['username'], 'UPDATE', 'LABORATORIOS', $this->CdLaboratorio, $dadosUpdate);
                $logs->Insert();

                $this->Result = true;
            } else {
                throw new Exception("Ops Parece que esse registro não existe mais na base de dados");
            }
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public static function RetornaDadosLaboratorio($cdlaboratorio)
    {
        $read = new \App\Conn\Read();
        $read->FullRead("SELECT L.* FROM LABORATORIOS L
                          WHERE L.CD_LABORATORIO = :C", "C=$cdlaboratorio");

        return $read->getResult()[0];
    }

    public function Delete()
    {
        try {
            $delete = new \App\Conn\Delete();
            $read = new \App\Conn\Read();

            $read->FullRead("SELECT * FROM LABORATORIOS WHERE CD_LABORATORIO = :C", "C=$this->CdLaboratorio");
            $dadosLaboratorio = $read->getResult()[0];

            $delete->ExeDelete("LABORATORIOS", "WHERE CD_LABORATORIO =:C", "C=$this->CdLaboratorio");

            if (!$delete->getResult()[0]) throw new Exception($delete->getResult()[1]);

            $logs = new \App\Models\Logs($_SESSION['username'], 'DELETE', 'LABORATORIOS', $this->CdLaboratorio, $dadosLaboratorio);
            $logs->Insert();

            $this->Result = true;
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
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
        $pesquisaAtivo = $arrayParam['pesquisaAtivo'] != '' ? ($arrayParam['pesquisaAtivo'] == 1 ? "S" : "N") : '';

        $read = new \App\Conn\Read();

        $query = "SELECT 
                    LABORATORIOS.CD_LABORATORIO,
                    LABORATORIOS.NM_LABORATORIO,
                    (CASE WHEN LABORATORIOS.FL_ATIVO = 'S' THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO, 
                    (SELECT COUNT(*) FROM LABORATORIOS WHERE 1=1) AS TOTAL_FILTERED,  
                    (SELECT COUNT(*) FROM LABORATORIOS) AS TOTAL_TABLE 
                    FROM 
                        LABORATORIOS
                    WHERE 
                        1=1
                    ";

        // $query = "SELECT LABORATORIOS.CD_LABORATORIO,
        //           LABORATORIOS.NM_LABORATORIO,
        //           (CASE WHEN LABORATORIOS.FL_ATIVO = 'S' THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO, 
        //           COUNT(LABORATORIOS.CD_LABORATORIO) OVER() AS TOTAL_FILTERED,  
        //           (SELECT COUNT(LABORATORIOS.CD_LABORATORIO) FROM LABORATORIOS) AS TOTAL_TABLE 
        //           FROM LABORATORIOS
        //           WHERE 1=1";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND LABORATORIOS.CD_LABORATORIO LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND LABORATORIOS.NM_LABORATORIO LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($pesquisaAtivo)) {
            $query .= " AND LABORATORIOS.FL_ATIVO LIKE '%$pesquisaAtivo%'";
        }


        if (!empty($orderBy)) {
            $query .= " ORDER BY $orderBy $orderAscDesc";
        }

        $query .= " LIMIT $start, $limit";

        $read->FullRead($query);

        return $read->getResult();
    }

    public function InserirLogo($Conn = false)
    {

        try {

            $dadosUpdate = [
                "ID_LOGO" => $this->idLogo
            ];
            
            $update = new \App\Conn\Update($Conn);
            $update->ExeUpdate("LABORATORIOS", $dadosUpdate, "WHERE CD_LABORATORIO = :C", "C=$this->CdLaboratorio");


            if (!$update->getResult()) {
                throw new Exception($update->getMessage());
            }

            $this->Result = true;
            $update->Commit();

            $logs = new \App\Models\Logs($_SESSION['username'], 'INSERT', 'LABORATORIOS', $this->CdLaboratorio, $dadosUpdate);
            $logs->Insert();

        } catch (Exception $e) {
            $update->Rollback();
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function getLogoId($Conn = false)
    {
        if (empty($this->CdLaboratorio)) {
            return null;
        }

        $read = new \App\Conn\Read($Conn);
        // $read->ExeRead("LABORATORIOS", "WHERE CD_LABORATORIO = :C", "C=$this->CdLaboratorio");
        $read->FullRead("SELECT L.ID_LOGO FROM LABORATORIOS L WHERE L.CD_LABORATORIO = :C", "C=$this->CdLaboratorio");
        return $read->getResult()[0];
    }

    public static function deleteImageById($imageID, $Conn = false)
    {
        if (empty($imageID)) {
            return null;
        }

        $read = new \App\Conn\Read();

        $read->FullRead("SELECT * FROM LABORATORIOS L WHERE L.ID_LOGO = :I", "I=$imageID");
        $dados = $read->getResult()[0];

        $update = new \App\Conn\Update($Conn);
        $update->ExeUpdate("LABORATORIOS", ["ID_LOGO" => ''], "WHERE ID_LOGO = :C", "C=$imageID");

        if (!$update->getResult()) return false;

        $logs = new \App\Models\Logs($_SESSION['username'], 'DELETE', 'LABORATORIOS', $dados['CD_LABORATORIO'], ["ID_LOGO" => $imageID]);
        $logs->Insert();

        return true;
    }

    public static function GeneralSearch($search)
    {
      $read = new \App\Conn\Read();
      $read->FullRead("SELECT L.* FROM LABORATORIOS L");
      return $read->getResult();
    }

    public function getCodigo()
    {
        return $this->CdLaboratorio;
    }

    public function getNome()
    {
        return $this->NmLaboratorio;
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
        $this->NmLaboratorio = $nome;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    public function setLogo($e)
    {
        $this->idLogo = $e;
    }
}
