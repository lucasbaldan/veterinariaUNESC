<?php

namespace App\Models;

use Exception;

class LogsFichaLPV 
{

  private $usuario;
  private $acao;
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


  function __construct($usuario, $acao, $cdfichalpv, $dtficha, $animal, $nmveterinarioremetente, $crmvveterinarioremetente, $nrtelveterinarioremetente, $dsemailveterinarioremetente, $nmcidadeveterinarioremetente,   $cdusuarioplantonista, $nmproprietario, $nrtelefoneproprietario, $cidadepropriedade, $dsespecie, $dsraca, $dssexo, $idade, $totalanimais, $qtdanimaisdoentes, $qtdanimaismortos, $dsMaterialRecebido, $dsdiagnosticopresuntivo, $flAvaliacaoTumoralComMargem, $dsnomeanimal, $dsepidemiologiahistoriaclinica, $dslesoesmacroscopicas, $dslesoeshistologicas, $dsdiagnostico, $dsrelatorio)
  {
    $this->usuario = $usuario;
    $this->acao = $acao;
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
  }

  public function Insert()
  {
    $insert = new \App\Conn\Insert();

    try {
      $insert->ExeInsert("logs", [
        "USUARIO" => $this->usuario,
        "DT_HORA" => date('Y-m-d H:i:s'),
        "ACAO" => $this->acao,
        "CD_FICHA_LPV" => $this->cdFichaLPV,
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

  public static function GeneralSearch($search)
  {
    $read = new \App\Conn\Read();
    if (!empty($search)) {
      $read->FullRead("SELECT L.* FROM LOGS L WHERE UPPER(CONCAT(F.CD_LOG, ' ', T.CD_FICHA_LPV)) LIKE UPPER(CONCAT('%', :P, '%')) ORDER BY L.CD_LOG ASC", "P=$search");
    } else {
      $read->FullRead("SELECT L.* FROM LOGS L");
    }
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
