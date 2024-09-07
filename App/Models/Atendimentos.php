<?php

namespace App\Models;

use Exception;

class Atendimentos
{

    private $codigo;
    private $data;
    private \App\Models\Animais $animal;
    private \App\Models\Pessoas $veterinarioRemetente;
    private \App\Models\Municipios $cidadeOrigem;
    private $idadeAnimalAno;
    private $idadeAnimalMeses;
    private $idadeAnimalDias;
    private $totalAnimais;
    private $AnimaisMortos;
    private $AnimaisDoentes;
    private $materialRecebido;
    private $diagnosticoPresuntivo;
    private $avalicaoTumoralMargem;
    private $epidemiologia;
    private $lessoesMacroscopias;
    private $lessoesHistologicas;
    private $diagnostico;
    private $relatorio;
    private $usuarioAcao;

    private $idImagem;

    private $Result;
    private $Message;
    private $Return;

    public function __construct(
        $data,
        $cdAnimal,
        $cdVeterinarioRemetente,
        $cdCidadeOrigem,
        $totalAnimais,
        $AnimaisMortos,
        $AnimaisDoentes,
        $materialRecebido,
        $diagnosticoPresuntivo,
        $avalicaoTumoralMargem,
        $epidemiologia,
        $lessoesMacroscopias,
        $lessoesHistologicas,
        $diagnostico,
        $relatorio,
        $idadeAno,
        $idadeMes,
        $idadeDia,
        $codigo = null
    ) {
        $this->codigo = $codigo;
        $this->data = $data;
        $this->animal = \App\Models\Animais::findById($cdAnimal);
        $this->veterinarioRemetente = \App\Models\Pessoas::findById($cdVeterinarioRemetente);
        $this->cidadeOrigem = \App\Models\Municipios::findById($cdCidadeOrigem);
        $this->totalAnimais = $totalAnimais;
        $this->AnimaisMortos = $AnimaisMortos;
        $this->AnimaisDoentes = $AnimaisDoentes;
        $this->materialRecebido = $materialRecebido;
        $this->diagnosticoPresuntivo = $diagnosticoPresuntivo;
        $this->avalicaoTumoralMargem = $avalicaoTumoralMargem;
        $this->epidemiologia = $epidemiologia;
        $this->lessoesMacroscopias = $lessoesMacroscopias;
        $this->lessoesHistologicas = $lessoesHistologicas;
        $this->diagnostico = $diagnostico;
        $this->relatorio = $relatorio;
        $this->idadeAnimalAno = $idadeAno;
        $this->idadeAnimalMeses = $idadeMes;
        $this->idadeAnimalDias = $idadeDia;
        $this->usuarioAcao = \App\Helpers\Sessao::getInfoSessao()['username'];
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

            return new self(
                $read->getResult()[0]['DT_FICHA'],
                $read->getResult()[0]['CD_ANIMAL'],
                $read->getResult()[0]['CD_PESSOA_VETERINARIO_REMETENTE'],
                $read->getResult()[0]['CD_CIDADE_PROPRIEDADE'],
                $read->getResult()[0]['TOTAL_ANIMAIS'],
                $read->getResult()[0]['QTD_ANIMAIS_MORTOS'],
                $read->getResult()[0]['QTD_ANIMAIS_DOENTES'],
                $read->getResult()[0]['DS_MATERIAL_RECEBIDO'],
                $read->getResult()[0]['DS_DIAGNOSTICO_PRESUNTIVO'],
                $read->getResult()[0]['FL_AVALIACAO_TUMORAL_COM_MARGEM'],
                $read->getResult()[0]['DS_EPIDEMIOLOGIA_HISTORIA_CLINICA'],
                $read->getResult()[0]['DS_LESOES_MACROSCOPICAS'],
                $read->getResult()[0]['DS_LESOES_HISTOLOGICAS'],
                $read->getResult()[0]['DS_DIAGNOSTICO'],
                $read->getResult()[0]['DS_RELATORIO'],
                $read->getResult()[0]['IDADE_ANIMAL_ANO'],
                $read->getResult()[0]['IDADE_ANIMAL_MES'],
                $read->getResult()[0]['IDADE_ANIMAL_DIA'],
                $read->getResult()[0]['CD_FICHA_LPV'],
            );
        } catch (Exception $e) {
            return new self('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
        }
    }

    public function getImagesIds($Conn = false)
    {
        if (empty($this->codigo)) {
            return null;
        }

        $read = new \App\Conn\Read($Conn);
        $read->ExeRead("IMAGENS_ATENDIMENTOS", "WHERE CD_ATENDIMENTO = :C", "C=$this->codigo");

        if ($read->getRowCount() == 0) return null;

        return array_column($read->getResult(), 'ID_IMAGEM');
    }

    public static function deleteImageById($imageID, $Conn = false)
    {
        if (empty($imageID)) {
            return null;
        }

        $read = new \App\Conn\Read();

        $read->FullRead("SELECT * FROM IMAGENS_ATENDIMENTOS WHERE ID_IMAGEM = :C", "C=$imageID");
        $dadosImagem = $read->getResult()[0];

        $delete = new \App\Conn\Delete($Conn = false);
        $delete->ExeDelete("IMAGENS_ATENDIMENTOS", "WHERE ID_IMAGEM = :C", "C=$imageID");

        if (!$delete->getResult()[0]) return false;

        $logs = new \App\Models\Logs($_SESSION['username'], 'DELETE', 'IMAGENS_ATENDIMENTOS', $dadosImagem['CD_IMAGEM_ATM'], $dadosImagem);
        $logs->Insert();

        return true;
    }

    public static function SelectGrid($arrayParam)
    {

        $start = $arrayParam['inicio'];
        $limit = $arrayParam['limit'];
        $orderBy = $arrayParam['orderBy'];
        $orderAscDesc = $arrayParam['orderAscDesc'];
        $pesquisaCodigo = $arrayParam['pesquisaCodigo'];
        $pesquisaDataInicio = $arrayParam['pesquisaDataInicio'];
        $pesquisaDataFim = $arrayParam['pesquisaDataFim'];
        $pesquisaNomeAnimal = $arrayParam['pesquisaNomeAnimal'];
        $pesquisaEspecieAnimal = $arrayParam['pesquisaEspecieAnimal'];
        $pesquisaRacaAnimal = $arrayParam['pesquisaRacaAnimal'];
        $pesquisaSexoAnimal = $arrayParam['pesquisaSexoAnimal'];

        $pesquisaCastrado = $arrayParam['pesquisaCastrado'];
        $pesquisaIdadeAnoInicio = $arrayParam['pesquisaIdadeAnoInicio'];
        $pesquisaIdadeAnoFim = $arrayParam['pesquisaIdadeAnoFim'];
        $pesquisaIdadeMesInicio = $arrayParam['pesquisaIdadeMesInicio'];
        $pesquisaIdadeMesFim = $arrayParam['pesquisaIdadeMesFim'];
        $pesquisaIdadeDiaInicio = $arrayParam['pesquisaIdadeDiaInicio'];
        $pesquisaIdadeDiaFim = $arrayParam['pesquisaIdadeDiaFim'];


        $pesquisaTutor = $arrayParam['pesquisaTutor'];
        $pesquisaVeterinario = $arrayParam['pesquisaVeterinario'];
        $pesquisaMunicipio = $arrayParam['pesquisaMunicipio'];
        $pesquisaMaterial = $arrayParam['pesquisaMaterial'];
        $pesquisaDiagnosticoPresuntivo = $arrayParam['pesquisaDiagnosticoPresuntivo'];
        $pesquisaAvaliacaoTumor = $arrayParam['pesquisaAvaliacaoTumor'];
        $pesquisaEpidemiologia = $arrayParam['pesquisaEpidemiologia'];
        $pesquisaLessaoMacro = $arrayParam['pesquisaLessaoMacro'];
        $pesquisaLessaoHisto = $arrayParam['pesquisaLessaoHisto'];
        $pesquisaDiagnostico = $arrayParam['pesquisaDiagnostico'];
        $pesquisaRelatorio = $arrayParam['pesquisaRelatorio'];

        $read = new \App\Conn\Read();

        $query = " SELECT 
                    FICHA_LPV.CD_FICHA_LPV,
                    FICHA_LPV.IDADE_ANIMAL_ANO,
                    FICHA_LPV.IDADE_ANIMAL_MES,
                    FICHA_LPV.IDADE_ANIMAL_DIA,
                    DATE_FORMAT(FICHA_LPV.DT_FICHA, '%d/%m/%Y') as DT_FICHA,
                    ANIMAIS.NM_ANIMAL,
                    ESPECIES.DESCRICAO as NM_ESPECIE,
                    RACAS.DESCRICAO as NM_RACA,
                    (CASE WHEN ANIMAIS.FL_CASTRADO = 'S' THEN 'Sim' WHEN ANIMAIS.FL_CASTRADO = 'N' THEN 'Não' ELSE 'Não Informado' END) AS CASTRADO,
                    (CASE WHEN ANIMAIS.SEXO = 'F' THEN 'Fêmea' WHEN ANIMAIS.SEXO = 'M' THEN 'Macho' ELSE '-' END) AS SEXO,
                    TUTOR.NM_PESSOA as NM_TUTOR,
                    VETERINARIO.NM_PESSOA as NM_VETERINARIO,
                    CIDADES.NOME as CIDADE_PROPRIEDADE,
                    FICHA_LPV.DS_MATERIAL_RECEBIDO,
                    FICHA_LPV.DS_DIAGNOSTICO_PRESUNTIVO,
                    (CASE WHEN FICHA_LPV.FL_AVALIACAO_TUMORAL_COM_MARGEM = 'S' THEN 'Sim' 
                        WHEN FICHA_LPV.FL_AVALIACAO_TUMORAL_COM_MARGEM = 'N' THEN 'Não' ELSE '-' END) AS FL_AVALIACAO_TUMORAL_COM_MARGEM,
                    FICHA_LPV.DS_EPIDEMIOLOGIA_HISTORIA_CLINICA,
                    FICHA_LPV.DS_LESOES_MACROSCOPICAS,
                    FICHA_LPV.DS_LESOES_HISTOLOGICAS,
                    FICHA_LPV.DS_DIAGNOSTICO,
                    FICHA_LPV.DS_RELATORIO,
                    FICHA_LPV.USUARIO_CRIACAO,
                    FICHA_LPV.USUARIO_ALTERACAO,
                    (SELECT COUNT(*) FROM FICHA_LPV) AS TOTAL_FILTERED,
                    (SELECT COUNT(*) FROM FICHA_LPV) AS TOTAL_TABLE 
                FROM 
                    FICHA_LPV
                INNER JOIN 
                    ANIMAIS ON FICHA_LPV.CD_ANIMAL = ANIMAIS.CD_ANIMAL
                LEFT JOIN 
                    ESPECIES ON ANIMAIS.CD_ESPECIE = ESPECIES.CD_ESPECIE
                LEFT JOIN 
                    RACAS ON ANIMAIS.CD_RACA = RACAS.CD_RACA
                LEFT JOIN 
                    PESSOAS TUTOR ON ANIMAIS.CD_PESSOA_TUTOR1 = TUTOR.CD_PESSOA
                LEFT JOIN 
                    PESSOAS VETERINARIO ON FICHA_LPV.CD_PESSOA_VETERINARIO_REMETENTE = VETERINARIO.CD_PESSOA
                LEFT JOIN 
                    CIDADES ON FICHA_LPV.CD_CIDADE_PROPRIEDADE = CIDADES.CD_CIDADE
                WHERE 
                    1=1 ";

        // $query = "  SELECT FICHA_LPV.CD_FICHA_LPV,
        // DATE_FORMAT(FICHA_LPV.DT_FICHA, '%d/%m/%Y') as DT_FICHA,
        // ANIMAIS.NM_ANIMAL,
        // TIPO_ANIMAL.DESCRICAO as NM_TIPO_ANIMAL,
        // ESPECIES.DESCRICAO as NM_ESPECIE,
        // RACAS.DESCRICAO as NM_RACA,
        // (CASE WHEN ANIMAIS.SEXO = 'F' THEN 'Fêmea' WHEN ANIMAIS.SEXO = 'M' THEN 'Macho' ELSE '-' END) AS SEXO,
        // TUTOR.NM_PESSOA as NM_TUTOR,
        // VETERINARIO.NM_PESSOA as NM_VETERINARIO,
        // CIDADES.NOME as CIDADE_PROPRIEDADE,
        // FICHA_LPV.DS_MATERIAL_RECEBIDO,
        // FICHA_LPV.DS_DIAGNOSTICO_PRESUNTIVO,
        // (CASE WHEN FICHA_LPV.FL_AVALIACAO_TUMORAL_COM_MARGEM = 'S' THEN 'Sim' WHEN FICHA_LPV.FL_AVALIACAO_TUMORAL_COM_MARGEM = 'N' THEN 'Não' ELSE '-' END) AS FL_AVALIACAO_TUMORAL_COM_MARGEM,
        // FICHA_LPV.DS_EPIDEMIOLOGIA_HISTORIA_CLINICA,
        // FICHA_LPV.DS_LESOES_MACROSCOPICAS,
        // FICHA_LPV.DS_LESOES_HISTOLOGICAS,
        // FICHA_LPV.DS_DIAGNOSTICO,
        // FICHA_LPV.DS_RELATORIO,

        // COUNT(FICHA_LPV.CD_FICHA_LPV) OVER() AS TOTAL_FILTERED,  
        // (SELECT COUNT(FICHA_LPV.CD_FICHA_LPV) FROM FICHA_LPV) AS TOTAL_TABLE 

        // FROM FICHA_LPV
        // INNER JOIN ANIMAIS ON (FICHA_LPV.CD_ANIMAL = ANIMAIS.CD_ANIMAL)
        // LEFT JOIN TIPO_ANIMAL ON (ANIMAIS.CD_TIPO_ANIMAL = TIPO_ANIMAL.CD_TIPO_ANIMAL)
        // LEFT JOIN ESPECIES ON (ANIMAIS.CD_ESPECIE = ESPECIES.CD_ESPECIE)
        // LEFT JOIN RACAS ON (ANIMAIS.CD_RACA = RACAS.CD_RACA)
        // LEFT JOIN PESSOAS TUTOR ON (ANIMAIS.CD_PESSOA_TUTOR1 = TUTOR.CD_PESSOA)
        // LEFT JOIN PESSOAS VETERINARIO ON (FICHA_LPV.CD_PESSOA_VETERINARIO_REMETENTE = VETERINARIO.CD_PESSOA)
        // LEFT JOIN CIDADES ON (FICHA_LPV.CD_CIDADE_PROPRIEDADE = CIDADES.CD_CIDADE)

        // WHERE 1=1 ";

        if (!empty($pesquisaCodigo)) $query .= " AND FICHA_LPV.CD_FICHA_LPV LIKE '%$pesquisaCodigo%'";
        if (!empty($pesquisaDataInicio)) $query .= " AND FICHA_LPV.DT_FICHA >= '$pesquisaDataInicio'";
        if (!empty($pesquisaDataFim)) $query .= " AND FICHA_LPV.DT_FICHA <= '$pesquisaDataFim'";
        if (!empty($pesquisaNomeAnimal)) $query .= " AND ANIMAIS.NM_ANIMAL LIKE '%$pesquisaNomeAnimal%'";
        if (!empty($pesquisaEspecieAnimal)) $query .= " AND ESPECIES.DESCRICAO LIKE '%$pesquisaEspecieAnimal%'";
        if (!empty($pesquisaRacaAnimal)) $query .= " AND RACAS.DESCRICAO LIKE '%$pesquisaRacaAnimal%'";
        if (!empty($pesquisaSexoAnimal)) $query .= " AND ANIMAIS.SEXO = '$pesquisaSexoAnimal'";
        if (!empty($pesquisaCastrado)) $query .= " AND ANIMAIS.FL_CASTRADO = '$pesquisaCastrado'";
        if (!empty($pesquisaIdadeAnoInicio)) $query .= " AND FICHA_LPV.IDADE_ANIMAL_ANO >= $pesquisaIdadeAnoInicio";
        if (!empty($pesquisaIdadeAnoFim)) $query .= " AND FICHA_LPV.IDADE_ANIMAL_ANO <= $pesquisaIdadeAnoFim";
        if (!empty($pesquisaIdadeMesInicio)) $query .= " AND FICHA_LPV.IDADE_ANIMAL_MES >= $pesquisaIdadeMesInicio";
        if (!empty($pesquisaIdadeMesFim)) $query .= " AND FICHA_LPV.IDADE_ANIMAL_MES <= $pesquisaIdadeMesFim";
        if (!empty($pesquisaIdadeDiaInicio)) $query .= " AND FICHA_LPV.IDADE_ANIMAL_DIA >= $pesquisaIdadeDiaInicio";
        if (!empty($pesquisaIdadeDiaFim)) $query .= " AND FICHA_LPV.IDADE_ANIMAL_DIA <= $pesquisaIdadeDiaFim";
        if (!empty($pesquisaTutor)) $query .= " AND TUTOR.NM_PESSOA LIKE '%$pesquisaTutor%'";
        if (!empty($pesquisaVeterinario)) $query .= " AND VETERINARIO.NM_PESSOA LIKE '%$pesquisaVeterinario%'";
        if (!empty($pesquisaMunicipio)) $query .= " AND CIDADES.NOME LIKE '%$pesquisaMunicipio%'";
        if (!empty($pesquisaMaterial)) $query .= " AND FICHA_LPV.DS_MATERIAL_RECEBIDO LIKE '%$pesquisaMaterial%'";
        if (!empty($pesquisaDiagnosticoPresuntivo)) $query .= " AND FICHA_LPV.DS_DIAGNOSTICO_PRESUNTIVO LIKE '%$pesquisaDiagnosticoPresuntivo%'";
        if (!empty($pesquisaAvaliacaoTumor)) $query .= " AND FICHA_LPV.FL_AVALIACAO_TUMORAL_COM_MARGEM = '$pesquisaAvaliacaoTumor'";
        if (!empty($pesquisaEpidemiologia)) $query .= " AND FICHA_LPV.DS_EPIDEMIOLOGIA_HISTORIA_CLINICA LIKE '%$pesquisaEpidemiologia%'";
        if (!empty($pesquisaLessaoMacro)) $query .= " AND FICHA_LPV.DS_LESOES_MACROSCOPICAS LIKE '%$pesquisaLessaoMacro%'";
        if (!empty($pesquisaLessaoHisto)) $query .= " AND FICHA_LPV.DS_LESOES_HISTOLOGICAS LIKE '%$pesquisaLessaoHisto%'";
        if (!empty($pesquisaDiagnostico)) $query .= " AND FICHA_LPV.DS_DIAGNOSTICO LIKE '%$pesquisaDiagnostico%'";
        if (!empty($pesquisaRelatorio)) $query .= " AND FICHA_LPV.DS_RELATORIO LIKE '%$pesquisaRelatorio%'";


        if (!empty($orderBy)) {
            $query .= " ORDER BY $orderBy $orderAscDesc";
        }

        if (!empty($start) && !empty($limit)) $query .= " LIMIT $start, $limit";

        $read->FullRead($query);

        return $read->getResult();
    }

    public function Inserir($Conn = false)
    {

        try {
            $insert = new \App\Conn\Insert($Conn);

            $dadosInsert = [
                "CD_FICHA_LPV" => $this->codigo,
                "DT_FICHA" => $this->data,
                "CD_ANIMAL" => $this->animal->getCodigo(),
                "CD_PESSOA_VETERINARIO_REMETENTE" => $this->veterinarioRemetente->getCodigo(),
                "CD_CIDADE_PROPRIEDADE" => $this->cidadeOrigem->getCodigo(),
                "TOTAL_ANIMAIS" => $this->totalAnimais,
                "QTD_ANIMAIS_MORTOS" => $this->AnimaisMortos,
                "QTD_ANIMAIS_DOENTES" => $this->AnimaisDoentes,
                "DS_MATERIAL_RECEBIDO" => $this->materialRecebido,
                "DS_DIAGNOSTICO_PRESUNTIVO" => $this->diagnosticoPresuntivo,
                "FL_AVALIACAO_TUMORAL_COM_MARGEM" => $this->avalicaoTumoralMargem,
                "DS_EPIDEMIOLOGIA_HISTORIA_CLINICA" => $this->epidemiologia,
                "DS_LESOES_MACROSCOPICAS" => $this->lessoesMacroscopias,
                "DS_LESOES_HISTOLOGICAS" => $this->lessoesHistologicas,
                "DS_DIAGNOSTICO" => $this->diagnostico,
                "DS_RELATORIO" => $this->relatorio,
                "IDADE_ANIMAL_ANO" => $this->idadeAnimalAno,
                "IDADE_ANIMAL_MES" => $this->idadeAnimalMeses,
                "IDADE_ANIMAL_DIA" => $this->idadeAnimalDias,
                "USUARIO_CRIACAO" => $this->usuarioAcao
            ];

            $insert->ExeInsert("FICHA_LPV", $dadosInsert);

            if (!$insert->getResult()) {
                throw new Exception($insert->getMessage());
            }
            $this->codigo = $insert->getLastInsert();

            $logs = new \App\Models\Logs($_SESSION['username'], 'INSERT', 'FICHA_LPV', $this->codigo, $dadosInsert);
            $logs->Insert();

            $this->Result = true;
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function InserirImagem()
    {

        try {
            $insert = new \App\Conn\Insert();

            $dadosInsert = [
                "CD_ATENDIMENTO" => $this->codigo,
                "ID_IMAGEM" => $this->idImagem
            ];

            $insert->ExeInsert("IMAGENS_ATENDIMENTOS", $dadosInsert);

            if (!$insert->getResult()) {
                throw new Exception($insert->getMessage());
            }

            $this->Result = true;
            $insert->Commit();
            $imageID = $insert->getLastInsert();

            $logs = new \App\Models\Logs($_SESSION['username'], 'INSERT', 'IMAGENS_ATENDIMENTOS', $imageID, $dadosInsert);
            $logs->Insert();

        } catch (Exception $e) {
            $insert->Rollback();
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function Atualizar($Conn = false)
    {
        try {
            $read = new \App\Conn\Read($Conn);

            $read->ExeRead("FICHA_LPV", "WHERE CD_FICHA_LPV = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $update = new \App\Conn\Update($Conn);

                $dadosUpdate = [
                    "DT_FICHA" => $this->data,
                    "CD_ANIMAL" => $this->animal->getCodigo(),
                    "CD_PESSOA_VETERINARIO_REMETENTE" => $this->veterinarioRemetente->getCodigo(),
                    "CD_CIDADE_PROPRIEDADE" => $this->cidadeOrigem->getCodigo(),
                    "TOTAL_ANIMAIS" => $this->totalAnimais,
                    "QTD_ANIMAIS_MORTOS" => $this->AnimaisMortos,
                    "QTD_ANIMAIS_DOENTES" => $this->AnimaisDoentes,
                    "DS_MATERIAL_RECEBIDO" => $this->materialRecebido,
                    "DS_DIAGNOSTICO_PRESUNTIVO" => $this->diagnosticoPresuntivo,
                    "FL_AVALIACAO_TUMORAL_COM_MARGEM" => $this->avalicaoTumoralMargem,
                    "DS_EPIDEMIOLOGIA_HISTORIA_CLINICA" => $this->epidemiologia,
                    "DS_LESOES_MACROSCOPICAS" => $this->lessoesMacroscopias,
                    "DS_LESOES_HISTOLOGICAS" => $this->lessoesHistologicas,
                    "DS_DIAGNOSTICO" => $this->diagnostico,
                    "DS_RELATORIO" => $this->relatorio,
                    "IDADE_ANIMAL_ANO" => $this->idadeAnimalAno,
                    "IDADE_ANIMAL_MES" => $this->idadeAnimalMeses,
                    "IDADE_ANIMAL_DIA" => $this->idadeAnimalDias,
                    "USUARIO_ALTERACAO" => $this->usuarioAcao
                ];

                $update->ExeUpdate("FICHA_LPV", $dadosUpdate, "WHERE CD_FICHA_LPV = :D", "D=$this->codigo");

                if (!$update->getResult()) {
                    throw new Exception($update->getMessage());
                }

                $logs = new \App\Models\Logs($_SESSION['username'], 'UPDATE', 'FICHA_LPV', $this->codigo, $dadosUpdate);
                $logs->Insert();

                $this->Result = true;
            } else {
                throw new Exception("Ops, Parece que esse registro não existe mais na base de dados!");
            }
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function Excluir($Conn = false)
    {

        try {
            $delete = new \App\Conn\Delete($Conn);
            $read = new \App\Conn\Read($Conn);

            $read->FullRead("SELECT * FROM FICHA_LPV WHERE CD_FICHA_LPV = :C", "C=$this->codigo");
            $dadosFicha = $read->getResult()[0];

            $delete->ExeDelete("FICHA_LPV", "WHERE CD_FICHA_LPV = :C", "C=$this->codigo");
            $delete->Commit();

            $logs = new \App\Models\Logs($_SESSION['username'], 'DELETE', 'FICHA_LPV', $this->codigo, $dadosFicha);
            $logs->Insert();

            $this->Result = true;
        } catch (Exception $e) {
            $this->Message = $e->getMessage();
            $delete->Rollback();
            $this->Result = false;
        }
    }

    // public function generalSearch($arrayParam){
    //     try{
    //         $colunas = $arrayParam['colunas'];
    //         $descricao = !empty($arrayParam['descricaoPesquisa']) ? $arrayParam['descricaoPesquisa'] : '';

    //         $read = new \App\Conn\Read();

    //         $query = "SELECT $colunas FROM ESPECIES WHERE 1=1";

    //         if(!empty($descricao)){
    //             $query .= " AND DESCRICAO LIKE '%$descricao%'";
    //         }

    //         $read->FullRead($query);
    //         $this->Result = true;
    //         $this->Return = $read->getResult();
    //     } catch(Exception $e){
    //         $this->Result = false;
    //         $this->Message = $e->getMessage();

    //     }
    // }


    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getAnimal()
    {
        return $this->animal;
    }

    public function getVeterinarioRemetente()
    {
        return $this->veterinarioRemetente;
    }

    public function getCidadeOrigem()
    {
        return $this->cidadeOrigem;
    }

    public function getTotalAnimais()
    {
        return $this->totalAnimais;
    }

    public function getAnimaisMortos()
    {
        return $this->AnimaisMortos;
    }

    public function getAnimaisDoentes()
    {
        return $this->AnimaisDoentes;
    }

    public function getMaterialRecebido()
    {
        return $this->materialRecebido;
    }

    public function getDiagnosticoPresuntivo()
    {
        return $this->diagnosticoPresuntivo;
    }

    public function getAvalicaoTumoralMargem()
    {
        return $this->avalicaoTumoralMargem;
    }

    public function getEpidemiologia()
    {
        return $this->epidemiologia;
    }

    public function getLessoesMacroscopias()
    {
        return $this->lessoesMacroscopias;
    }

    public function getLessoesHistologicas()
    {
        return $this->lessoesHistologicas;
    }

    public function getDiagnostico()
    {
        return $this->diagnostico;
    }

    public function getRelatorio()
    {
        return $this->relatorio;
    }

    public function getIdadeAno()
    {
        return $this->idadeAnimalAno;
    }

    public function getIdadeMes()
    {
        return $this->idadeAnimalMeses;
    }

    public function getIdadeDia()
    {
        return $this->idadeAnimalDias;
    }

    public function getResult()
    {
        return $this->Result;
    }

    public function getMessage()
    {
        return $this->Message;
    }

    public function getReturn()
    {
        return $this->Return;
    }

    public function setImagem($e)
    {
        $this->idImagem = $e;
    }
}
