<?php

namespace App\Models;

use Exception;

class FormularioLPV
{
  private $cdFichaLPV;
  private $dtFicha;
  private $animal;
  private $nmVeterinarioRemetente;
  private $crmvVeterinarioRemetente;
  private $nrTelVeterinarioRemetente;
  private $dsEmailVeterinarioRemetente;
  private $nmCidadeVeterinarioRemetente;
  private $cdUsuarioPlantonista;
  private $nmProprietario;
  private $nrTelefoneProprietario;
  private $cidadePropriedade;
  private $dsEspecie;
  private $dsRaca;
  private $dsSexo;
  private $idade;
  private $totalAnimais;
  private $qtdAnimaisDoentes;
  private $qtdAnimaisMortos;
  private $dsMaterialRecebido;
  private $dsDiagnosticoPresuntivo;
  private $flAvaliacaoTumoralComMargem;
  private $dsNomeAnimal;
  private $dsEpidemiologiaHistoriaClinica;
  private $dsLesoesMacroscopicas;
  private $dsLesoesHistologicas;
  private $dsDiagnostico;
  private $dsRelatorio;
  private $Return;
  private $Result;
  private $Message;


  function __construct($dtficha, $animal, $nmveterinarioremetente, $crmvveterinarioremetente, $nrtelveterinarioremetente, $dsemailveterinarioremetente, $nmcidadeveterinarioremetente,   $cdusuarioplantonista, $nmproprietario, $nrtelefoneproprietario, $cidadepropriedade, $dsespecie, $dsraca, $dssexo, $idade, $totalanimais, $qtdanimaisdoentes, $qtdanimaismortos, $dsMaterialRecebido, $dsdiagnosticopresuntivo, $flAvaliacaoTumoralComMargem, $dsnomeanimal, $dsepidemiologiahistoriaclinica, $dslesoesmacroscopicas, $dslesoeshistologicas, $dsdiagnostico, $dsrelatorio, $cdfichalpv = null)
  {
    $this->cdFichaLPV = $cdfichalpv;
    $this->dtFicha = $dtficha;
    $this->animal = $animal;
    $this->nmVeterinarioRemetente = $nmveterinarioremetente;
    $this->crmvVeterinarioRemetente = $crmvveterinarioremetente;
    $this->nrTelVeterinarioRemetente = $nrtelveterinarioremetente;
    $this->dsEmailVeterinarioRemetente = $dsemailveterinarioremetente;
    $this->nmCidadeVeterinarioRemetente = $nmcidadeveterinarioremetente;
    $this->cdUsuarioPlantonista = $cdusuarioplantonista;
    $this->nmProprietario = $nmproprietario;
    $this->nrTelefoneProprietario = $nrtelefoneproprietario;
    $this->cidadePropriedade = $cidadepropriedade;
    $this->dsEspecie = $dsespecie;
    $this->dsRaca = $dsraca;
    $this->dsSexo = $dssexo;
    $this->idade = $idade;
    $this->totalAnimais = $totalanimais;
    $this->qtdAnimaisDoentes = $qtdanimaisdoentes;
    $this->qtdAnimaisMortos = $qtdanimaismortos;
    $this->dsMaterialRecebido = $dsMaterialRecebido;
    $this->dsDiagnosticoPresuntivo = $dsdiagnosticopresuntivo;
    $this->flAvaliacaoTumoralComMargem = $flAvaliacaoTumoralComMargem;
    $this->dsNomeAnimal = $dsnomeanimal;
    $this->dsEpidemiologiaHistoriaClinica = $dsepidemiologiahistoriaclinica;
    $this->dsLesoesMacroscopicas = $dslesoesmacroscopicas;
    $this->dsLesoesHistologicas = $dslesoeshistologicas;
    $this->dsDiagnostico = $dsdiagnostico;
    $this->dsRelatorio = $dsrelatorio;

    // $this->cdPessoa = $codPessoa;
    // $this->nmPessoa = mb_strtoupper($nmPessoa);
    // $this->login = $login_pessoa;
    // $this->senha = md5($senha_pessoa);
  }

  public static function findById($id)
  {
      try {
          if (empty($id)) {
              throw new Exception("Objeto vazio");
          }
          $read = new \App\Conn\Read();
          $read->ExeRead("FICHA_LPV", "WHERE CD_FICHA_LPV = :C LIMIT 1", "C=$id");
  
          if ($read->getRowCount() == 0) {
              throw new Exception("Não foi possível Localizar o Registro na Base de Dados.");
          }
  
          $result = $read->getResult()[0];
          return new self(
              $result['DT_FICHA'],
              $result['ANIMAL'],
              $result['NM_VET_REMETENTE'],
              $result['CRMV_VET_REMETENTE'],
              $result['NR_TEL_VET_REMETENTE'],
              $result['DS_EMAIL_VET_REMETENTE'],
              $result['NM_CIDADE_VET_REMETENTE'],
              $result['CD_USUARIO_PLANTONISTA'],
              $result['NM_PROPRIETARIO'],
              $result['NR_TELEFONE_PROPRIETARIO'],
              $result['CIDADE_PROPRIEDADE'],
              $result['DS_ESPECIE'],
              $result['DS_RACA'],
              $result['DS_SEXO'],
              $result['IDADE'],
              $result['TOTAL_ANIMAIS'],
              $result['QTD_ANIMAIS_DOENTES'],
              $result['QTD_ANIMAIS_MORTOS'],
              $result['DS_MATERIAL_RECEBIDO'],
              $result['DS_DIAGNOSTICO_PRESUNTIVO'],
              $result['FL_AVALIACAO_TUMORAL_COM_MARGEM'],
              $result['DS_NOME_ANIMAL'],
              $result['DS_EPIDEMIOLOGIA_HISTORIA_CLINICA'],
              $result['DS_LESOES_MACROSCOPICAS'],
              $result['DS_LESOES_HISTOLOGICAS'],
              $result['DS_DIAGNOSTICO'],
              $result['DS_RELATORIO'],
              $result['CD_FICHA_LPV']
          );
      } catch (Exception $e) {
          return new self('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
      }
  }
  


  public function Insert()
  {
    $insert = new \App\Conn\Insert();

    try {
      $insert->ExeInsert("ficha_lpv", [
        // "CD_FICHA_LPV" => $this->cdFichaLPV,
        "DT_FICHA" => $this->dtFicha,
        "ANIMAL" => $this->animal,
        "NM_VET_REMETENTE" => $this->nmVeterinarioRemetente,
        "NR_TEL_VET_REMETENTE" => $this->nrTelVeterinarioRemetente,
        "DS_EMAIL_VET_REMETENTE" => $this->dsEmailVeterinarioRemetente,
        "CRMV_VET_REMETENTE" => $this->crmvVeterinarioRemetente,
        "NM_CIDADE_VET_REMETENTE" => $this->nmCidadeVeterinarioRemetente,
        "CD_USUARIO_PLANTONISTA" => $this->cdUsuarioPlantonista,
        "NM_PROPRIETARIO" => $this->nmProprietario,
        "NR_TELEFONE_PROPRIETARIO" => $this->nrTelefoneProprietario,
        "CIDADE_PROPRIEDADE" => $this->cidadePropriedade,
        "DS_ESPECIE" => $this->dsEspecie,
        "DS_RACA" => $this->dsRaca,
        "DS_SEXO" => $this->dsSexo,
        "IDADE" => $this->idade,
        "TOTAL_ANIMAIS" => $this->totalAnimais,
        "QTD_ANIMAIS_MORTOS" => $this->qtdAnimaisMortos,
        "QTD_ANIMAIS_DOENTES" => $this->qtdAnimaisDoentes,
        "DS_MATERIAL_RECEBIDO" => $this->dsMaterialRecebido,
        "DS_DIAGNOSTICO_PRESUNTIVO" => $this->dsDiagnosticoPresuntivo,
        "FL_AVALIACAO_TUMORAL_COM_MARGEM" => $this->flAvaliacaoTumoralComMargem,
        "DS_NOME_ANIMAL" => $this->dsNomeAnimal,
        "DS_EPIDEMIOLOGIA_HISTORIA_CLINICA" => $this->dsEpidemiologiaHistoriaClinica,
        "DS_LESOES_MACROSCOPICAS" => $this->dsLesoesMacroscopicas,
        "DS_LESOES_HISTOLOGICAS" => $this->dsLesoesHistologicas,
        "DS_DIAGNOSTICO" => $this->dsDiagnostico,
        "DS_RELATORIO" => $this->dsRelatorio
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
      $read->ExeRead("ficha_lpv", "WHERE CD_FICHA_LPV = :C", "C=$this->cdFichaLPV");
      $dadosFicha = $read->getResult()[0] ?? [];
      if ($dadosFicha) {
        $dadosUpdate = [
          "DT_FICHA" => $this->dtFicha,
          "ANIMAL" => $this->animal,
          "NM_VET_REMETENTE" => $this->nmVeterinarioRemetente,
          "NR_TEL_VET_REMETENTE" => $this->nrTelVeterinarioRemetente,
          "DS_EMAIL_VET_REMETENTE" => $this->dsEmailVeterinarioRemetente,
          "CRMV_VET_REMETENTE" => $this->crmvVeterinarioRemetente,
          "NM_CIDADE_VET_REMETENTE" => $this->nmCidadeVeterinarioRemetente,
          "CD_USUARIO_PLANTONISTA" => $this->cdUsuarioPlantonista,
          "NM_PROPRIETARIO" => $this->nmProprietario,
          "NR_TELEFONE_PROPRIETARIO" => $this->nrTelefoneProprietario,
          "CIDADE_PROPRIEDADE" => $this->cidadePropriedade,
          "DS_ESPECIE" => $this->dsEspecie,
          "DS_RACA" => $this->dsRaca,
          "DS_SEXO" => $this->dsSexo,
          "IDADE" => $this->idade,
          "TOTAL_ANIMAIS" => $this->totalAnimais,
          "QTD_ANIMAIS_MORTOS" => $this->qtdAnimaisMortos,
          "QTD_ANIMAIS_DOENTES" => $this->qtdAnimaisDoentes,
          "DS_MATERIAL_RECEBIDO" => $this->dsMaterialRecebido,
          "DS_DIAGNOSTICO_PRESUNTIVO" => $this->dsDiagnosticoPresuntivo,
          "FL_AVALIACAO_TUMORAL_COM_MARGEM" => $this->flAvaliacaoTumoralComMargem,
          "DS_NOME_ANIMAL" => $this->dsNomeAnimal,
          "DS_EPIDEMIOLOGIA_HISTORIA_CLINICA" => $this->dsEpidemiologiaHistoriaClinica,
          "DS_LESOES_MACROSCOPICAS" => $this->dsLesoesMacroscopicas,
          "DS_LESOES_HISTOLOGICAS" => $this->dsLesoesHistologicas,
          "DS_DIAGNOSTICO" => $this->dsDiagnostico,
          "DS_RELATORIO" => $this->dsRelatorio
        ];

        $update = new \App\Conn\Update();

        $update->ExeUpdate("FICHA_LPV", $dadosUpdate, "WHERE CD_FICHA_LPV = :C", "C=$this->cdFichaLPV");
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


  public static function GeneralSearch($search)
  {
    $read = new \App\Conn\Read();
    if (!empty($search)) {
      $read->FullRead("SELECT F.* FROM FICHA_LPV F  WHERE UPPER(CONCAT(F.CD_FICHA_LPV, ' ', T.NM_EMPRESA)) LIKE UPPER(CONCAT('%', :P, '%')) ORDER BY T.NM_EMPRESA ASC", "P=$search");
    } else {
      $read->FullRead("SELECT T.* FROM EMPRESAS T");
    }
    return $read->getResult();
  }

  public static function RetornarDadosFichaLPV($cdFichaLPV)
  {
    $read = new \App\Conn\Read();
    $read->FullRead("SELECT F.* FROM FICHA_LPV F  WHERE F.CD_FICHA_LPV = :C", "C=$cdFichaLPV");

    return $read->getResult()[0];
  }

  public static function RetornarDadosRelatorioFichaLPV($cdFichaLPV)
  {
    $read = new \App\Conn\Read();
    $read->FullRead("SELECT F.*, P.NM_PESSOA AS NM_VETERINARIO, A.NM_ANIMAL, C.NOME AS NM_CIDADE
    FROM FICHA_LPV F  
    LEFT JOIN PESSOAS P ON P.CD_PESSOA = F.CD_PESSOA_VETERINARIO_REMETENTE
    INNER JOIN ANIMAIS A ON A.CD_ANIMAL = F.CD_ANIMAL
    INNER JOIN CIDADES C ON C.CD_CIDADE = CD_CIDADE_PROPRIEDADE
    WHERE F.CD_FICHA_LPV = :C", "C=$cdFichaLPV");

    return $read->getResult()[0];
  }

  public static function Delete($cdFichaLPV)
  {
    $delete = new \App\Conn\Delete();

    $delete->ExeDelete("FICHA_LPV", "WHERE CD_FICHA_LPV =:C", "C=$cdFichaLPV");
    $deletado = !empty($delete->getResult());

    if ($deletado) {
      return true;
    } else {
      return false;
    }
  }

  public static function RetornaFichasFiltradas($cdAnimal, $cdCidade, $cdVetRemetente, $dtInicialFicha, $dtFinalFicha, $flAvaliacaoTumoral)
  {
    $read = new \App\Conn\Read();

    $sql = "SELECT F.*, C.NOME AS NM_CIDADE, P.NM_PESSOA AS NM_VETERINARIO_REMETENTE 
            FROM FICHA_LPV F
            INNER JOIN CIDADES C ON C.CD_CIDADE = F.CD_CIDADE_PROPRIEDADE
            INNER JOIN PESSOAS P ON P.CD_PESSOA = F.CD_PESSOA_VETERINARIO_REMETENTE
            WHERE 1=1";

    if (!empty($cdAnimal)) $sql .= " AND F.CD_ANIMAL = $cdAnimal";
    if (!empty($cdCidade)) $sql .= " AND F.CD_CIDADE_PROPRIEDADE = $cdCidade";
    if (!empty($cdVetRemetente)) $sql .= " AND F.CD_PESSOA_VETERINARIO_REMETENTE = $cdVetRemetente";
    if (!empty($flAvaliacaoTumoral)) $sql .= " AND F.FL_AVALIACAO_TUMORAL_COM_MARGEM = '$flAvaliacaoTumoral'";

    if (!empty($dtInicialFicha) && empty($dtFinalFicha)) {
      $sql .= " AND F.DT_FICHA > '$dtInicialFicha'";
    } else if (empty($dtInicialFicha) && !empty($dtFinalFicha)) {
      $sql .= " AND F.DT_FICHA < '$dtFinalFicha'";
    } else if (!empty($dtInicialFicha) && !empty($dtFinalFicha)) {
      $sql .= " AND F.DT_FICHA BETWEEN '$dtInicialFicha' AND '$dtFinalFicha'";
    }

    $read->FullRead($sql);

    return $read->getResult();
  }

  public function getCdFichaLPV()
  {
    return $this->cdFichaLPV;
  }

  public function setCdFichaLPV($cdFichaLPV)
  {
    $this->cdFichaLPV = $cdFichaLPV;
  }

  public function getDtFicha()
  {
    return $this->dtFicha;
  }

  public function setDtFicha($dtFicha)
  {
    $this->dtFicha = $dtFicha;
  }

  public function getAnimal()
  {
    return $this->animal;
  }

  public function setAnimal($animal)
  {
    $this->animal = $animal;
  }

  public function getNmVeterinarioRemetente()
  {
    return $this->nmVeterinarioRemetente;
  }

  public function setNmVeterinarioRemetente($nmVeterinarioRemetente)
  {
    $this->nmVeterinarioRemetente = $nmVeterinarioRemetente;
  }

  public function getCrmvVeterinarioRemetente()
  {
    return $this->crmvVeterinarioRemetente;
  }

  public function setCrmvVeterinarioRemetente($crmvVeterinarioRemetente)
  {
    $this->crmvVeterinarioRemetente = $crmvVeterinarioRemetente;
  }

  public function getNrTelVeterinarioRemetente()
  {
    return $this->nrTelVeterinarioRemetente;
  }

  public function setNrTelVeterinarioRemetente($nrTelVeterinarioRemetente)
  {
    $this->nrTelVeterinarioRemetente = $nrTelVeterinarioRemetente;
  }

  public function getDsEmailVeterinarioRemetente()
  {
    return $this->dsEmailVeterinarioRemetente;
  }

  public function setDsEmailVeterinarioRemetente($dsEmailVeterinarioRemetente)
  {
    $this->dsEmailVeterinarioRemetente = $dsEmailVeterinarioRemetente;
  }

  public function getNmCidadeVeterinarioRemetente()
  {
    return $this->nmCidadeVeterinarioRemetente;
  }

  public function setNmCidadeVeterinarioRemetente($nmCidadeVeterinarioRemetente)
  {
    $this->nmCidadeVeterinarioRemetente = $nmCidadeVeterinarioRemetente;
  }

  public function getCdUsuarioPlantonista()
  {
    return $this->cdUsuarioPlantonista;
  }

  public function setCdUsuarioPlantonista($cdUsuarioPlantonista)
  {
    $this->cdUsuarioPlantonista = $cdUsuarioPlantonista;
  }

  public function getNmProprietario()
  {
    return $this->nmProprietario;
  }

  public function setNmProprietario($nmProprietario)
  {
    $this->nmProprietario = $nmProprietario;
  }

  public function getNrTelefoneProprietario()
  {
    return $this->nrTelefoneProprietario;
  }

  public function setNrTelefoneProprietario($nrTelefoneProprietario)
  {
    $this->nrTelefoneProprietario = $nrTelefoneProprietario;
  }

  public function getCidadePropriedade()
  {
    return $this->cidadePropriedade;
  }

  public function setCidadePropriedade($cidadePropriedade)
  {
    $this->cidadePropriedade = $cidadePropriedade;
  }

  public function getDsEspecie()
  {
    return $this->dsEspecie;
  }

  public function setDsEspecie($dsEspecie)
  {
    $this->dsEspecie = $dsEspecie;
  }

  public function getDsRaca()
  {
    return $this->dsRaca;
  }

  public function setDsRaca($dsRaca)
  {
    $this->dsRaca = $dsRaca;
  }

  public function getDsSexo()
  {
    return $this->dsSexo;
  }

  public function setDsSexo($dsSexo)
  {
    $this->dsSexo = $dsSexo;
  }

  public function getIdade()
  {
    return $this->idade;
  }

  public function setIdade($idade)
  {
    $this->idade = $idade;
  }

  public function getTotalAnimais()
  {
    return $this->totalAnimais;
  }

  public function setTotalAnimais($totalAnimais)
  {
    $this->totalAnimais = $totalAnimais;
  }

  public function getQtdAnimaisDoentes()
  {
    return $this->qtdAnimaisDoentes;
  }

  public function setQtdAnimaisDoentes($qtdAnimaisDoentes)
  {
    $this->qtdAnimaisDoentes = $qtdAnimaisDoentes;
  }

  public function getQtdAnimaisMortos()
  {
    return $this->qtdAnimaisMortos;
  }

  public function setQtdAnimaisMortos($qtdAnimaisMortos)
  {
    $this->qtdAnimaisMortos = $qtdAnimaisMortos;
  }

  public function getDsMaterialRecebido()
  {
    return $this->dsMaterialRecebido;
  }

  public function setDsMaterialRecebido($dsMaterialRecebido)
  {
    $this->dsMaterialRecebido = $dsMaterialRecebido;
  }

  public function getDsDiagnosticoPresuntivo()
  {
    return $this->dsDiagnosticoPresuntivo;
  }

  public function setDsDiagnosticoPresuntivo($dsDiagnosticoPresuntivo)
  {
    $this->dsDiagnosticoPresuntivo = $dsDiagnosticoPresuntivo;
  }

  public function getFlAvaliacaoTumoralComMargem()
  {
    return $this->flAvaliacaoTumoralComMargem;
  }

  public function setFlAvaliacaoTumoralComMargem($flAvaliacaoTumoralComMargem)
  {
    $this->flAvaliacaoTumoralComMargem = $flAvaliacaoTumoralComMargem;
  }

  public function getDsNomeAnimal()
  {
    return $this->dsNomeAnimal;
  }

  public function setDsNomeAnimal($dsNomeAnimal)
  {
    $this->dsNomeAnimal = $dsNomeAnimal;
  }

  public function getDsEpidemiologiaHistoriaClinica()
  {
    return $this->dsEpidemiologiaHistoriaClinica;
  }

  public function setDsEpidemiologiaHistoriaClinica($dsEpidemiologiaHistoriaClinica)
  {
    $this->dsEpidemiologiaHistoriaClinica = $dsEpidemiologiaHistoriaClinica;
  }

  public function getDsLesoesMacroscopicas()
  {
    return $this->dsLesoesMacroscopicas;
  }

  public function setDsLesoesMacroscopicas($dsLesoesMacroscopicas)
  {
    $this->dsLesoesMacroscopicas = $dsLesoesMacroscopicas;
  }

  public function getDsLesoesHistologicas()
  {
    return $this->dsLesoesHistologicas;
  }

  public function setDsLesoesHistologicas($dsLesoesHistologicas)
  {
    $this->dsLesoesHistologicas = $dsLesoesHistologicas;
  }

  public function getDsDiagnostico()
  {
    return $this->dsDiagnostico;
  }

  public function setDsDiagnostico($dsDiagnostico)
  {
    $this->dsDiagnostico = $dsDiagnostico;
  }

  public function getDsRelatorio()
  {
    return $this->dsRelatorio;
  }

  public function setDsRelatorio($dsRelatorio)
  {
    $this->dsRelatorio = $dsRelatorio;
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
