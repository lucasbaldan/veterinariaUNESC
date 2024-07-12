<?php

namespace App\Models;

use Exception;

class Usuarios
{
  private $CdUsuario;
  private \App\Models\Pessoas $CdPessoa;
  private $Usuario;
  private $Senha;
  private \App\Models\GruposUsuarios $CdGrupoUsuarios;
  private $FlAtivo;
  private $Return;
  private $Result;
  private $Message;

  function __construct($cdpessoa, $usuario, $senha, $cdgrupousuarios, $flativo, $cdusuario = null)
  {
    $this->CdPessoa = \App\Models\Pessoas::findById($cdpessoa);
    $this->Usuario = $usuario;
    // $this->Senha = md5($senha);
    $this->Senha = $senha;
    $this->CdGrupoUsuarios = \App\Models\GruposUsuarios::findById($cdgrupousuarios);
    $this->FlAtivo = $flativo;
    $this->CdUsuario = $cdusuario;
  }

  public static function findById($id)
  {
    try {
      if (empty($id)) {
        throw new Exception("Objeto vazio");
      }

      $read = new \App\Conn\Read();

      $read->ExeRead("USUARIOS", "WHERE CD_USUARIO = :C LIMIT 1", "C=$id");

      if ($read->getRowCount() == 0) {
        throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
      }

      return new self(
        $read->getResult()[0]['CD_PESSOA'],
        $read->getResult()[0]['USUARIO'],
        '',
        $read->getResult()[0]['CD_GRUPO_USUARIOS'],
        $read->getResult()[0]['FL_ATIVO'],
        $read->getResult()[0]['CD_USUARIO']
      );
    } catch (Exception $e) {
      return new self('', '', '', '', '');
    }
  }

  public function Insert()
  {
    $insert = new \App\Conn\Insert();

    try {
      $insert->ExeInsert("USUARIOS", [
        "CD_PESSOA" => $this->CdPessoa->getCodigo(),
        "USUARIO" => $this->Usuario,
        "SENHA" => $this->Senha,
        "FL_ATIVO" => $this->FlAtivo,
        "CD_GRUPO_USUARIOS" => $this->CdGrupoUsuarios->getCodigo(),
      ]);

      if (!$insert->getResult()) {
        throw new Exception($insert->getMessage());
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
      $read->ExeRead("USUARIOS", "WHERE CD_USUARIO = :C", "C=$this->CdUsuario");
      $dadosFicha = $read->getResult()[0] ?? [];
      if ($dadosFicha) {
        if (empty($this->Senha)) {
          $dadosUpdate = [
            "CD_PESSOA" => $this->CdPessoa->getCodigo(),
            "USUARIO" => $this->Usuario,
            // "SENHA" => $this->Senha,
            "FL_ATIVO" => $this->FlAtivo,
            "CD_GRUPO_USUARIOS" => $this->CdGrupoUsuarios->getCodigo(),
          ];
        } else {
          $dadosUpdate = [
            "CD_PESSOA" => $this->CdPessoa->getCodigo(),
            "USUARIO" => $this->Usuario,
            "SENHA" => $this->Senha,
            "FL_ATIVO" => $this->FlAtivo,
            "CD_GRUPO_USUARIOS" => $this->CdGrupoUsuarios->getCodigo(),
          ];
        }

        $update = new \App\Conn\Update();

        $update->ExeUpdate("USUARIOS", $dadosUpdate, "WHERE CD_USUARIO = :C", "C=$this->CdUsuario");
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
    $read->FullRead("SELECT U.CD_USUARIO, U.CD_PESSOA, P.NM_PESSOA, U.USUARIO, U.FL_ATIVO, U.CD_GRUPO_USUARIOS, G.NM_GRUPO_USUARIOS
    FROM USUARIOS U
    INNER JOIN PESSOAS P ON P.CD_PESSOA = U.CD_PESSOA
    INNER JOIN GRUPOS_USUARIOS G ON G.CD_GRUPO_USUARIOS = U.CD_GRUPO_USUARIOS
    WHERE U.CD_USUARIO = :C", "C=$cdUsuario");

    return $read->getResult()[0];
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

  public static function efetuarLogin($Usuario, $senha)
  {
    $read = new \App\Conn\Read();

    $read->ExeRead("USUARIOS", "WHERE USUARIO = :U AND SENHA = :S LIMIT 1", "U=$Usuario&S=$senha");

    if ($read->getResult()) {
      return new self(
        $read->getResult()[0]['CD_PESSOA'],
        $read->getResult()[0]['USUARIO'],
        '',
        $read->getResult()[0]['CD_GRUPO_USUARIOS'],
        $read->getResult()[0]['FL_ATIVO'],
        $read->getResult()[0]['CD_USUARIO']
      );
    } else {
      return new self('', '', '', '', '');
    }
  }

  public static function SelectGrid($arrayParam)
  {

    $start = $arrayParam['inicio'];
    $limit = $arrayParam['limit'];
    $orderBy = $arrayParam['orderBy'];
    $orderAscDesc = $arrayParam['orderAscDesc'];
    $pesquisaCodigo = $arrayParam['pesquisaCodigo'];
    $pesquisaNmUsuario = $arrayParam['pesquisaNmUsuario'];
    $pesquisaGrupoUsuario = $arrayParam['pesquisaGrupoUsuario'];
    $pesquisaAtivo = $arrayParam['pesquisaAtivo'];

    $read = new \App\Conn\Read();

    $query = "SELECT 
              USUARIOS.CD_USUARIO,
              PESSOAS.NM_PESSOA AS NM_USUARIO,
              GRUPOS_USUARIOS.NM_GRUPO_USUARIOS,
              (CASE WHEN USUARIOS.FL_ATIVO = 'S' THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO, 
              (SELECT COUNT(*) FROM USUARIOS) AS TOTAL_FILTERED,
              (SELECT COUNT(*) FROM USUARIOS) AS TOTAL_TABLE 
          FROM 
              USUARIOS
          LEFT JOIN 
              GRUPOS_USUARIOS ON GRUPOS_USUARIOS.CD_GRUPO_USUARIOS = USUARIOS.CD_GRUPO_USUARIOS
          INNER JOIN 
              PESSOAS ON PESSOAS.CD_PESSOA = USUARIOS.CD_PESSOA
          WHERE 
              1=1;";

    // $query = "SELECT USUARIOS.CD_USUARIO,
    //     PESSOAS.NM_PESSOA AS NM_USUARIO,
    //     GRUPOS_USUARIOS.NM_GRUPO_USUARIOS,
    //     (CASE WHEN USUARIOS.FL_ATIVO = 'S' THEN 'Sim' ELSE 'Não' END) AS FL_ATIVO, 
    //     COUNT(USUARIOS.CD_USUARIO) OVER() AS TOTAL_FILTERED,  
    //     (SELECT COUNT(USUARIOS.CD_USUARIO) FROM USUARIOS) AS TOTAL_TABLE 
    //     FROM USUARIOS
    //     LEFT JOIN GRUPOS_USUARIOS ON (GRUPOS_USUARIOS.CD_GRUPO_USUARIOS = USUARIOS.CD_GRUPO_USUARIOS)
    //     INNER JOIN PESSOAS ON (PESSOAS.CD_PESSOA = USUARIOS.CD_PESSOA)
    //     WHERE 1=1";

    if (!empty($pesquisaCodigo)) {
      $query .= " AND USUARIOS.CD_USUARIO LIKE '%$pesquisaCodigo%'";
    }
    if (!empty($pesquisaNmUsuario)) {
      $query .= " AND USUARIOS.NM_USUARIO LIKE '%$pesquisaNmUsuario%'";
    }
    if (!empty($pesquisaGrupoUsuario)) {
      $query .= " AND GRUPOS_USUARIOS.NM_GRUPO_USUARIOS LIKE '%$pesquisaGrupoUsuario%'";
    }
    if (!empty($pesquisaAtivo)) {
      $query .= " AND USUARIOS.FL_ATIVO LIKE '%$pesquisaAtivo%'";
    }


    if (!empty($orderBy)) {
      $query .= " ORDER BY $orderBy $orderAscDesc";
    }

    $query .= " LIMIT $start, $limit";

    $read->FullRead($query);

    return $read->getResult();
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

  public function getCodigo()
  {
    return $this->CdUsuario;
  }

  public function getPessoa()
  {
    return $this->CdPessoa;
  }

  public function getGrupoUsuario()
  {
    return $this->CdGrupoUsuarios;
  }

  public function getLogin()
  {
    return $this->Usuario;
  }

  public function getFlAtivo()
  {
    return $this->FlAtivo;
  }
}
