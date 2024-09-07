<?php

namespace App\Models;

use Exception;

class Logs
{
  private $usuario;
  private $acao;
  private $tabela;
  private $codigo;
  private $dados;
  private $Return;
  private $Result;
  private $Message;


  function __construct($usuario, $acao, $tabela, $codigo, $dados)
  {
    $this->usuario = $usuario;
    $this->acao = $acao;
    $this->tabela = $tabela;
    $this->codigo = $codigo;
    $this->dados = json_encode($dados);
  }

  public function Insert()
  {
    $insert = new \App\Conn\Insert();

    try {
      $insert->ExeInsert("LOGS", [
        "USUARIO" => $this->usuario,
        "DT_HORA" => date('Y-m-d H:i:s'),
        "ACAO" => $this->acao,
        "TABELA" => $this->tabela,
        "CODIGO" => $this->codigo,
        "DADOS" => $this->dados
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

  public static function GeneralSearch($search)
  {
    $read = new \App\Conn\Read();
    $read->FullRead("SELECT L.* FROM LOGS L");
    return $read->getResult();
  }

  public static function PesquisaFiltrada($tabela, $codigo, $dtInicial, $dtFinal)
  {
    $read = new \App\Conn\Read();
    $sql = "SELECT L.* FROM LOGS L WHERE 1=1";

    if(!empty($tabela)) $sql .= " AND L.TABELA = '$tabela'";
    if(!empty($codigo)) $sql .= " AND L.CODIGO = $codigo";

    if(!empty($dtInicial) && empty($dtFinal)) $sql .= " AND DATE(L.DT_HORA) > '$dtInicial'";
    if(empty($dtInicial) && !empty($dtFinal)) $sql .= " AND DATE(L.DT_HORA) < '$dtFinal'";
    if(!empty($dtInicial) && !empty($dtFinal)) $sql .= " AND DATE(L.DT_HORA) BETWEEN '$dtInicial' AND '$dtFinal'";

    $sql .= " ORDER BY L.CD_LOG LIMIT 250";
    $read->FullRead($sql);

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
}
