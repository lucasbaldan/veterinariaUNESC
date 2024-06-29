<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Atendimentos
{

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = " ficha_lpv.CD_FICHA_LPV";
            if ($orderBy == 1) $orderBy = "ficha_lpv.DT_FICHA";
            if ($orderBy == 2) $orderBy = "tipo_animal.descricao";
            if ($orderBy == 3) $orderBy = "especies.descricao";
            if ($orderBy == 4) $orderBy = "racas.descricao";
            if ($orderBy == 5) $orderBy = "animais.sexo";
            if ($orderBy == 6) $orderBy = "dono.nm_pessoa";
            if ($orderBy == 7) $orderBy = "veterinario.nm_pessoa";
            if ($orderBy == 8) $orderBy = "cidades.nome";
            if ($orderBy == 9) $orderBy = "ficha_lpv.DS_MATERIAL_RECEBIDO";
            if ($orderBy == 10) $orderBy = "ficha_lpv.DS_DIAGNOSTICO_PRESUNTIVO";
            if ($orderBy == 11) $orderBy = "ficha_lpv.FL_AVALIACAO_TUMORAL_COM_MARGEM";
            if ($orderBy == 12) $orderBy = "ficha_lpv.DS_EPIDEMIOLOGIA_HISTORIA_CLINICA";
            if ($orderBy == 13) $orderBy = "ficha_lpv.DS_LESOES_MACROSCOPICAS";
            if ($orderBy == 14) $orderBy = "ficha_lpv.DS_LESOES_HISTOLOGICAS";
            if ($orderBy == 15) $orderBy = "ficha_lpv.DS_DIAGNOSTICO";
            if ($orderBy == 16) $orderBy = "ficha_lpv.DS_RELATORIO";

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaDescricao" => !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '',
                "pesquisaTipoAnimal" => !empty($grid['columns'][2]['search']['value']) ? $grid['columns'][2]['search']['value'] : '',
                "pesquisaDono" => !empty($grid['columns'][3]['search']['value']) ? $grid['columns'][3]['search']['value'] : '',
                "pesquisaEspecie" => !empty($grid['columns'][4]['search']['value']) ? $grid['columns'][4]['search']['value'] : '',
                "pesquisaRaca" => !empty($grid['columns'][5]['search']['value']) ? $grid['columns'][5]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\Atendimentos::SelectGrid($parametrosBusca); 
            $dados = [
                "draw" => (int)$grid['draw'],
                "recordsTotal" => isset($dadosSelect[0]['total_table']) ? $dadosSelect[0]['total_table'] : 0,
                "recordsFiltered" => isset($dadosSelect[0]['total_filtered']) ? $dadosSelect[0]['total_filtered'] : 0,
                "data" => $dadosSelect
            ];


            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $dados];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function controlar(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();

            //INPUTUS DADOS ANIMAL
            $codigo = !empty($dadosForm['cdAnimal']) ? $dadosForm['cdAnimal'] : '';
            $nome = !empty($dadosForm['animal']) ? $dadosForm['animal'] : '';
            $cdTipoAnimal = isset($dadosForm['select2tipoAnimal']) ? $dadosForm['select2tipoAnimal'] : '';
            $cdEspecie = isset($dadosForm['select2especieAnimal']) ? $dadosForm['select2especieAnimal'] : '';
            $cdRaca = isset($dadosForm['select2racaAnimal']) ? $dadosForm['select2racaAnimal'] : '';
            $dsSexo = !empty($dadosForm['dsSexo']) ? $dadosForm['dsSexo'] : '';
            $idade = !empty($dadosForm['idade']) ? $dadosForm['idade'] : '';
            $anoNascimento = !empty($dadosForm['anoNascimento']) ? $dadosForm['anoNascimento'] : '';
            
            // INPUTS DA PESSOA DONA DO ANIMAL
            $donoNaoDeclarado = isset($dadosForm['donoNaoDeclarado']) ? 'S' : 'N';
            $cdPessoa = !empty($dadosForm['cdPessoa']) ? $dadosForm['cdPessoa'] : '';
            $alterouPessoa = !empty($dadosForm['alterouPessoa']) ? $dadosForm['alterouPessoa'] : '';
            $nomePessoa = isset($dadosForm['nmPessoa']) ? $dadosForm['nmPessoa'] : '';
            $cpf = isset($dadosForm['cpfPessoa']) ? $dadosForm['cpfPessoa'] : '';
            $dataNascimento = isset($dadosForm['dataNascimento']) ? $dadosForm['dataNascimento'] : '';
            $nrTelefone = isset($dadosForm['nrTelefone']) ? $dadosForm['nrTelefone'] : '';
            $email = isset($dadosForm['dsEmail']) ? $dadosForm['dsEmail'] : '';
            $nrCRMV = isset($dadosForm['nrCRMV']) ? $dadosForm['nrCRMV'] : '';
            $select2cdCidade = isset($dadosForm['select2cdCidade']) ? $dadosForm['select2cdCidade'] : '';
            $select2cdBairro = isset($dadosForm['select2cdBairro']) ? $dadosForm['select2cdBairro'] : '';
            $select2cdLogradouro = isset($dadosForm['select2cdLogradouro']) ? $dadosForm['select2cdLogradouro'] : '';

            $cad = new \App\Models\Animais($nome, $donoNaoDeclarado, $cdTipoAnimal, $cdEspecie, $cdRaca, $dsSexo, $idade, $anoNascimento, $dono = null, null, $codigo);
            if (empty($codigo)) {
                $cad->Inserir();
            } else {
                $cad->Atualizar();
            }

            if(!$cad->getResult()){
                throw new Exception($cad->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function excluir(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();

            $codigo = !empty($dadosForm['cdAnimal']) ? $dadosForm['cdAnimal'] : '';

            if (empty($codigo)) {
                throw new Exception("Houve um erro ao processo a requisição<br>Tente novamente mais tarde");
            }

            $cad = new \App\Models\Animais('','','','','','','','','','', $codigo);
            $cad->Excluir();
            

            if(!$cad->getResult()){
                throw new Exception($cad->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}
