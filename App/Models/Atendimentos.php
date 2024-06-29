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
                $read->getResult()[0]['CD_FICHA_LPV']
            );
        } catch (Exception $e) {
            return new self('','','','','','','','','','','','','','','');
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
        $pesquisaTipoAnimal = $arrayParam['pesquisaTipoAnimal'];
        $pesquisaEspecie = $arrayParam['pesquisaEspecie'];
        $pesquisaRaca = $arrayParam['pesquisaRaca'];
        $pesquisaDono = $arrayParam['pesquisaDono'];

        $read = new \App\Conn\Read();

        $query = "  SELECT ficha_lpv.CD_FICHA_LPV,
                    ficha_lpv.DT_FICHA,
                    animais.nm_animal,
                    tipo_animal.descricao as nm_tipo_animal,
                    especies.descricao as nm_especie,
                    racas.descricao as nm_raca,
                    animais.sexo,
                    dono.nm_pessoa as nm_dono,
                    veterinario.nm_pessoa as nm_veterinario,
                    cidades.nome as cidade_propridade,
                    ficha_lpv.DS_MATERIAL_RECEBIDO,
                    ficha_lpv.DS_DIAGNOSTICO_PRESUNTIVO,
                    ficha_lpv.FL_AVALIACAO_TUMORAL_COM_MARGEM,
                    ficha_lpv.DS_EPIDEMIOLOGIA_HISTORIA_CLINICA,
                    ficha_lpv.DS_LESOES_MACROSCOPICAS,
                    ficha_lpv.DS_LESOES_HISTOLOGICAS,
                    ficha_lpv.DS_DIAGNOSTICO,
                    ficha_lpv.DS_RELATORIO,

                    COUNT(ficha_lpv.CD_FICHA_LPV) OVER() AS total_filtered,  
                    (SELECT COUNT(ficha_lpv.CD_FICHA_LPV) FROM ficha_lpv) AS total_table 

                    FROM ficha_lpv
                    INNER JOIN animais ON (ficha_lpv.CD_ANIMAL = animais.cd_animal)
                    LEFT JOIN tipo_animal ON (animais.cd_tipo_animal = tipo_animal.cd_tipo_animal)
                    LEFT JOIN especies ON (animais.cd_especie = especies.cd_especie)
                    LEFT JOIN racas ON (animais.cd_raca = racas.cd_raca)
                    LEFT JOIN pessoas dono ON (animais.cd_pessoa_dono1 = dono.cd_pessoa)
                    LEFT JOIN pessoas veterinario ON (ficha_lpv.CD_PESSOA_VETERINARIO_REMETENTE = veterinario.cd_pessoa)
                    LEFT JOIN cidades ON (ficha_lpv.CD_CIDADE_PROPRIEDADE = cidades.cd_cidade)";

        if (!empty($pesquisaCodigo)) {
            $query .= " AND animais.cd_animal LIKE '%$pesquisaCodigo%'";
        }
        if (!empty($pesquisaDescricao)) {
            $query .= " AND animais.nm_animal LIKE '%$pesquisaDescricao%'";
        }
        if (!empty($orderBy)) {
            $query .= " ORDER BY $orderBy $orderAscDesc";
        }

        $query .= " LIMIT $start, $limit";

        $read->FullRead($query);

        return $read->getResult();
    }

    public function Inserir()
    {

        try {
            $conn = \App\Conn\Conn::getConn(true);
            $insert = new \App\Conn\Insert($conn);

            $dadosInsert = [
                "DT_FICHA" => $this->codigo,
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
                "DS_LESOES_MACROSCOPICAS" => $this-> lessoesMacroscopias,
                "DS_LESOES_HISTOLOGICAS" => $this->lessoesHistologicas,
                "DS_DIAGNOSTICO" => $this->diagnostico,
                "DS_RELATORIO" => $this->relatorio,
                "CD_FICHA_LPV" => $this->codigo
            ];

            $insert->ExeInsert("FICHA_LPV", $dadosInsert);

            if (!$insert->getResult()) {
                throw new Exception($insert->getMessage());
            }

            $insert->Commit();
            $this->Result = true;
        } catch (Exception $e) {
            $this->Result = false;
            $this->Message = $e->getMessage();
            $insert->Rollback();
        }
    }

    public function Atualizar()
    {
        try {
            $read = new \App\Conn\Read();

            $read->ExeRead("FICHA_LPV", "WHERE CD_FICHA_LPV = :D", "D=$this->codigo");
            $dadosCadastro = $read->getResult()[0] ?? [];
            if ($dadosCadastro) {

                $conn = \App\Conn\Conn::getConn(true);
                $update = new \App\Conn\Update($conn);

                $dadosUpdate = [
                "DT_FICHA" => $this->codigo,
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
                "DS_LESOES_MACROSCOPICAS" => $this-> lessoesMacroscopias,
                "DS_LESOES_HISTOLOGICAS" => $this->lessoesHistologicas,
                "DS_DIAGNOSTICO" => $this->diagnostico,
                "DS_RELATORIO" => $this->relatorio,
                "CD_FICHA_LPV" => $this->codigo
                ];

                $update->ExeUpdate("FICHA_LPV", $dadosUpdate, "WHERE CD_FICHA_LPV = :D", "D=$this->codigo");

                if (!$update->getResult()) {
                    throw new Exception($update->getMessage());
                }
                $update->Commit();
                $this->Result = true;
            } else {
                throw new Exception("Ops, Parece que esse registro não existe mais na base de dados!");
            }
        } catch (Exception $e) {
            $update->Rollback();
            $this->Result = false;
            $this->Message = $e->getMessage();
        }
    }

    public function Excluir()
    {

        try {
            $conn = \App\Conn\Conn::getConn();
            $delete = new \App\Conn\Delete($conn);

            $delete->ExeDelete("FICHA_LPV", "WHERE CD_FICHA_LPV = :C", "C=$this->codigo");

            $delete->Commit();
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


    public function getCodigo() {
        return $this->codigo;
    }

    public function getData() {
        return $this->data;
    }

    public function getAnimal() {
        return $this->animal;
    }

    public function getVeterinarioRemetente() {
        return $this->veterinarioRemetente;
    }

    public function getCidadeOrigem() {
        return $this->cidadeOrigem;
    }

    public function getTotalAnimais() {
        return $this->totalAnimais;
    }

    public function getAnimaisMortos() {
        return $this->AnimaisMortos;
    }

    public function getAnimaisDoentes() {
        return $this->AnimaisDoentes;
    }

    public function getDiagnosticoPresuntivo() {
        return $this->diagnosticoPresuntivo;
    }

    public function getAvalicaoTumoralMargem() {
        return $this->avalicaoTumoralMargem;
    }

    public function getEpidemiologia() {
        return $this->epidemiologia;
    }

    public function getLessoesMacroscopias() {
        return $this->lessoesMacroscopias;
    }

    public function getLessoesHistologicas() {
        return $this->lessoesHistologicas;
    }

    public function getDiagnostico() {
        return $this->diagnostico;
    }

    public function getRelatorio() {
        return $this->relatorio;
    }

    public function getResult() {
        return $this->Result;
    }

    public function getMessage() {
        return $this->Message;
    }

    public function getReturn() {
        return $this->Return;
    }
}
