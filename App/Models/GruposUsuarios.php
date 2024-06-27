<?php

namespace App\Models;

use Exception;

class GruposUsuarios
{
  private $CdGrupoUsuarios;
  private $NmGrupoUsuarios;
  private $Permissoes;
  private $FlAtivo;
  private $Return;
  private $Result;
  private $Message;


  function __construct($nmgrupousuarios, $permissoes, $flativo, $cdgrupousuarios = null)
  {
    $this->CdGrupoUsuarios = $cdgrupousuarios;
    $this->NmGrupoUsuarios = $nmgrupousuarios;
    $this->Permissoes = $permissoes;
    $this->FlAtivo = $flativo;
  }

  public static function findById($id)
  {
    try {
      if (empty($id)) {
        throw new Exception("Objeto vazio");
      }

      $read = new \App\Conn\Read();

      $read->ExeRead("GRUPOS_USUARIOS", "WHERE CD_GRUPO_USUARIOS = :C LIMIT 1", "C=$id");

      if ($read->getRowCount() == 0) {
        throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
      }

      return new self(
        $read->getResult()[0]['NM_GRUPO_USUARIOS'],
        $read->getResult()[0]['PERMISSOES'],
        $read->getResult()[0]['CD_GRUPO_USUARIOS'],
        $read->getResult()[0]['FL_ATIVO'],
      );
    } catch (Exception $e) {
      return new self('', '', '', '', '');
    }
  }

  public function Insert()
  {
    $insert = new \App\Conn\Insert();

    try {
      $insert->ExeInsert("grupos_usuarios", [
        // "CD_GRUPO_USUARIOS" => $this->CdGrupoUsuarios,
        "NM_GRUPO_USUARIOS" => $this->NmGrupoUsuarios,
        "PERMISSOES" => $this->Permissoes,
        "FL_ATIVO" => $this->FlAtivo,
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
      $read->ExeRead("grupos_usuarios", "WHERE CD_GRUPO_USUARIOS = :C", "C=$this->CdGrupoUsuarios");
      $dadosFicha = $read->getResult()[0] ?? [];
      if ($dadosFicha) {
        $dadosUpdate = [
          "NM_GRUPO_USUARIOS" => $this->NmGrupoUsuarios,
          "PERMISSOES" => $this->Permissoes,
          "FL_ATIVO" => $this->FlAtivo,
        ];

        $update = new \App\Conn\Update();

        $update->ExeUpdate("grupos_usuarios", $dadosUpdate, "WHERE CD_GRUPO_USUARIOS = :C", "C=$this->CdGrupoUsuarios");
        $atualizado = !empty($update->getResult());

        if (!$atualizado) {
          throw new Exception($update->getMessage());
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


  public function GeneralSearch($search)
  {
    $colunas = !empty($search['COLUNAS']) ? $search['COLUNAS'] : '*';

    $read = new \App\Conn\Read();
    $query = "SELECT $colunas
              FROM GRUPOS_USUARIOS ";

    $query .= "LIMIT 100";

    $read->FullRead($query);

    if (empty($read->getResult())) {
      return false;
    } else {
      $this->Result = true;
      $this->Return = $read->getResult();
      return $read->getResult();
    }
  }

  public static function RetornaDadosGrupoUsuarios($cdGrupoUsuarios)
  {
    $read = new \App\Conn\Read();
    $read->FullRead("SELECT G.* FROM grupos_usuarios G  WHERE G.CD_GRUPO_USUARIOS = :C ASC", "C=$cdGrupoUsuarios");

    return $read->getResult();
  }

  public static function Delete($cdGrupoUsuarios)
  {
    $delete = new \App\Conn\delete();

    $delete->ExeDelete("grupos_usuarios", "WHERE CD_GRUPO_USUARIOS =:C", "C=$cdGrupoUsuarios");
    $deletado = !empty($delete->getResult());

    if ($deletado) {
      return true;
    } else {
      return false;
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

  public function GetCodigo()
  {
    return $this->CdGrupoUsuarios;
  }

  public function GetNome()
  {
    return $this->NmGrupoUsuarios;
  }

  public function GetPermissoes()
  {
    return $this->Permissoes;
  }
}
