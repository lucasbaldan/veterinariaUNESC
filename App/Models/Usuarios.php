<?php

namespace App\Models;

use Exception;

class Usuarios
{
  private $CdUsuario;
  private $CdPessoa;
  private $Usuario;
  private $Senha;
  private $CdGrupoUsuarios;
  private $Return;
  private $Result;
  private $Message;

  function __construct($cdpessoa, $usuario, $senha, $cdgrupousuarios, $cdusuario = null)
  {
    $this->CdPessoa = $cdpessoa;
    $this->Usuario = $usuario;
    $this->Senha = md5($senha);
    $this->CdGrupoUsuarios = $cdgrupousuarios;
    $this->CdUsuario = $cdusuario;
  }

  public function Insert()
  {
    $insert = new \App\Conn\Insert();

    try {
      $insert->ExeInsert("usuarios", [
        "CD_PESSOA" => $this->CdPessoa,
        "USUARIO" => $this->Usuario,
        "SENHA" => $this->Senha,
        "SENHA" => $this->CdGrupoUsuarios,
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
      $read->ExeRead("usuarios", "WHERE CD_USUARIO = :C", "C=$this->CdUsuario");
      $dadosFicha = $read->getResult()[0] ?? [];
      if ($dadosFicha) {
        $dadosUpdate = [
          "CD_PESSOA" => $this->CdPessoa,
          "USUARIO" => $this->Usuario,
          "SENHA" => $this->Senha,
          "CD_GRUPO_USUARIOS" => $this->CdGrupoUsuarios,
        ];

        $update = new \App\Conn\Update();

        $update->ExeUpdate("usuarios", $dadosUpdate, "WHERE CD_USUARIO = :C", "C=$this->CdUsuario");
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

  public static function GeneralSearch()
  {
    $read = new \App\Conn\Read();
    $read->FullRead("SELECT U.CD_USUARIO, U.CD_PESSOA, P.NM_PESSOA, U.USUARIO, FL_ATIVO, U.CD_GRUPO_USUARIOS, G.NM_GRUPO_USUARIOS
                    FROM usuarios U
                    INNER JOIN pessoas P ON P.CD_PESSOA = U.CD_PESSOA
                    INNER JOIN grupos_usuarios G ON G.CD_GRUPO_USUARIOS = U.CD_GRUPO_USUARIOS 
                    ORDER BY U.USUARIO ASC");

    return $read->getResult();
  }

  public static function RetornaDadosUsuario($cdUsuario)
  {
    $read = new \App\Conn\Read();
    $read->FullRead("SELECT U.CD_USUARIO, U.CD_PESSOA, P.NM_PESSOA, U.USUARIO, FL_ATIVO, U.CD_GRUPO_USUARIOS, G.NM_GRUPO_USUARIOS
    FROM usuarios U
    INNER JOIN pessoas P ON P.CD_PESSOA = U.CD_PESSOA
    INNER JOIN grupos_usuarios G ON G.CD_GRUPO_USUARIOS = U.CD_GRUPO_USUARIOS
    WHERE U.CD_USUARIO = :C", "C=$cdUsuario");

    return $read->getResult();
  }

  public static function Delete($cdUsuario)
  {
    $delete = new \App\Conn\delete();

    $delete->ExeDelete("usuarios", "WHERE CD_USUARIO =:C", "C=$cdUsuario");
    $deletado = !empty($delete->getResult());

    if ($deletado) {
      return true;
    } else {
      return false;
    }
  }

  public static function AtivarDesativarUsuario($cdUsuario, $acao)
  {
    $update = new \App\Conn\Update();

    if ($acao == 'DESATIVAR') {
      $dado = ["FL_ATIVO" => "N"];
    } else {
      $dado = ["FL_ATIVO" => "S"];
    }

    $update->ExeUpdate("usuarios", $dado, "WHERE CD_USUARIO = :C", "C=$cdUsuario");
    $atualizado = !empty($update->getResult());

    if ($atualizado) {
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
