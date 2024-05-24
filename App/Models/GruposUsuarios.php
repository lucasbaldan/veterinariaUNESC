<?php

namespace App\Models;

use Exception;

class GruposUsuarios
{
  private $CdGrupoUsuarios;
  private $NmGrupoUsuarios;
  private $FlAcessar;
  private $FlEditar;
  private $FlExcluir;
  private $dsRelatorio;
  private $Return;
  private $Result;
  private $Message;


  function __construct($nmgrupousuarios, $flacessar, $fleditar, $flexcluir, $cdgrupousuarios = null)
  {
    $this->CdGrupoUsuarios = $cdgrupousuarios;
    $this->NmGrupoUsuarios = $nmgrupousuarios;
    $this->FlAcessar = $flacessar;
    $this->FlEditar = $fleditar;
    $this->FlExcluir = $flexcluir;
  }



  public function Insert()
  {
    $insert = new \App\Conn\Insert();

    try {
      $insert->ExeInsert("grupos_usuarios", [
        // "CD_GRUPO_USUARIOS" => $this->CdGrupoUsuarios,
        "NM_GRUPO_USUARIOS" => $this->NmGrupoUsuarios,
        "FL_ACESSAR" => $this->FlAcessar,
        "FL_EDITAR" => $this->FlEditar,
        "FL_EXCLUIR" => $this->FlExcluir,
      ]);

      if (!$insert->getResult()) {
        throw new Exception($insert->getError());
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
          // "CD_GRUPO_USUARIOS" => $this->dtFicha,
          "NM_GRUPO_USUARIOS" => $this->NmGrupoUsuarios,
          "FL_ACESSAR" => $this->FlAcessar,
          "FL_EDITAR" => $this->FlEditar,
          "FL_EXCLUIR" => $this->FlExcluir,
        ];

        $update = new \App\Conn\Update();

        $update->ExeUpdate("grupos_usuarios", $dadosUpdate, "WHERE CD_GRUPO_USUARIOS = :C", "C=$this->CdGrupoUsuarios");
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
      $read->FullRead("SELECT G.* FROM grupos_usuarios G  WHERE UPPER(CONCAT(F.CD_GRUPO_USUARIOS, ' ', T.NM_GRUPO_USUARIOS)) LIKE UPPER(CONCAT('%', :P, '%')) ORDER BY T.NM_GRUPO_USUARIOS ASC", "P=$search");
    } else {
      $read->FullRead("SELECT G.* FROM grupos_usuarios G");
    }
    return $read->getResult();
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
}
