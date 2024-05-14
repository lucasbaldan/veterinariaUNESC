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



  public function Insert()
  {
    $insert = new \App\Conn\Insert();
    
    try {
      $insert->ExeInsert("FICHA_LPV", [
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
      $read->ExeRead("FICHA_LPV", "WHERE CD_FICHA_LPV = :C", "C=$this->cdFichaLPV");
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
      $read->FullRead("SELECT F.* FROM FICHA_LPV F  WHERE UPPER(CONCAT(F.CD_FICHA_LPV, ' ', T.NM_EMPRESA)) LIKE UPPER(CONCAT('%', :P, '%')) ORDER BY T.NM_EMPRESA ASC", "P=$search");
    } else {
      $read->FullRead("SELECT T.* FROM EMPRESAS T");
    }
    return $read->getResult();
  }

  public static function listarPessoas($codigo)
  {
    $read = new \App\Conn\Read();
    if ($codigo) {
      $read->FullRead("SELECT *
                    FROM PESSOAS P 
                    WHERE P.CD_PESSOA =:C", "C=$codigo");
    } else {
      $read->FullRead("SELECT P.CD_PESSOA, NM_PESSOA, E.NM_EMPRESA, P.SENHA, P.LOGIN
                    FROM PESSOAS P 
                    INNER JOIN EMPRESAS E ON (P.CD_EMPRESA = E.CD_EMPRESA);");
    }
    return $read->getResult();
  }

  public static function listarLogins($codPessoa)
  {
    $read = new \App\Conn\Read();

    $read->FullRead("SELECT *
                    FROM PESSOAS P 
                    WHERE P.CD_PESSOA =:C", "C=$codPessoa");
    return $read->getResult();
  }
  public static function validarLogin($login, $senha, $token = null)
  {
    $conn = \App\Conn\Conn::getConn(true);
    $read = new \App\Conn\Read($conn);

    $read->FullRead("SELECT DISTINCT P.CD_PESSOA, P.NM_PESSOA 
   FROM PESSOAS P
   WHERE P.LOGIN =:C AND P.SENHA =:E", "C=$login&E=$senha");

    if ($read->getRowCount() > 0) {
      //( SELECT MAX(C.CD_CHAMADA) FROM CHAMADAS C WHERE C.CD_PROFESSOR = P.CD_PESSOA) as tem_reg_professor, ( SELECT MAX(C2.CD_CHAMADA) FROM CHAMADAS C2  WHERE C2.CD_ALUNO = P.CD_PESSOA) as tem_reg_aluno

      if ($token) {
        $update = new \App\Conn\Update($conn);
        $cdPessoa = $read->getResult()[0]["CD_PESSOA"];

        $update->ExeUpdate("PESSOAS", ["TOKEN_DISPOSITIVO" => $token], "WHERE CD_PESSOA =:C", "C=$cdPessoa");

        if (!$update->getResult()) {
          $update->Rollback();
          return false;
        }
        $update->Commit();
      }
      return $read->getResult();
    } else {
      $read = null;
      return false;
    }
  }

  public static function excluirPessoas($delete, $codPessoa)
  {
    $delete->ExeDelete("PESSOAS", "WHERE CD_PESSOA =:C", "C=$codPessoa");
    $deletado = !empty($delete->getResult());

    if ($deletado) {
      return true;
    } else {
      return false;
    }
  }

  public static function buscaTokens($conn = null)
  {
    $read = new \App\Conn\Read($conn);
    $read->FullRead("SELECT C.CD_CHAMADA, P.TOKEN_DISPOSITIVO, 'INICIO' AS HORA_AULA
    FROM PESSOAS P 
    INNER JOIN CHAMADAS C ON P.CD_PESSOA = C.CD_ALUNO
    WHERE C.DT_CHAMADA = CURRENT_DATE()
            
    AND DATE_FORMAT(DATE_SUB(concat(C.DT_CHAMADA,' ', C.HR_INICIAL), INTERVAL 20 MINUTE), '%Y-%m-%d %H:%i:%s')  <= CURRENT_TIMESTAMP()
    AND DATE_FORMAT(concat(C.DT_CHAMADA,' ', C.HR_INICIAL), '%Y-%m-%d %H:%i:%s') >= CURRENT_TIMESTAMP()        
                  
    AND P.TOKEN_DISPOSITIVO IS NOT NULL
    AND (C.FL_NOTIFICACAO_INICIO IS NULL OR C.FL_NOTIFICACAO_INICIO = 'N')
    GROUP BY C.CD_CHAMADA, P.TOKEN_DISPOSITIVO, HORA_AULA
    UNION ALL
    SELECT C.CD_CHAMADA, P.TOKEN_DISPOSITIVO, 'FIM' AS HORA_AULA 
    FROM PESSOAS P 
    INNER JOIN CHAMADAS C ON P.CD_PESSOA = C.CD_ALUNO
    WHERE C.DT_CHAMADA = CURRENT_DATE()
    
    AND DATE_FORMAT(DATE_SUB(concat(C.DT_CHAMADA,' ', C.HR_FINAL), INTERVAL 15 MINUTE), '%Y-%m-%d %H:%i:%s')  <= CURRENT_TIMESTAMP()
    AND DATE_FORMAT(concat(C.DT_CHAMADA,' ', C.HR_FINAL), '%Y-%m-%d %H:%i:%s') >= CURRENT_TIMESTAMP()
    
    AND P.TOKEN_DISPOSITIVO IS NOT NULL
    AND C.FL_PRESENTE = 'N'
    AND (C.FL_NOTIFICACAO_SAIDA IS NULL OR C.FL_NOTIFICACAO_SAIDA = 'N')
    GROUP BY C.CD_CHAMADA, P.TOKEN_DISPOSITIVO, HORA_AULA");

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
