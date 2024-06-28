<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Animais
{

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = "animais.cd_animal";
            if ($orderBy == 1) $orderBy = "animais.nm_animal";
            if ($orderBy == 2) $orderBy = "tipo_animal.descricao";
            if ($orderBy == 3) $orderBy = "animais.cd_animal";
            if ($orderBy == 4) $orderBy = "especies.descricao";
            if ($orderBy == 5) $orderBy = "racas.descricao";

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

            $dadosSelect = \App\Models\Animais::SelectGrid($parametrosBusca); 
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
            

            if (empty($alterouPessoa) || empty($donoNaoDeclarado)) {
                throw new Exception("Erro ao processar Requisição <br> Tente novamente mais tarde!");
            }

            if (empty($nome) || empty($dsSexo)) {
                throw new Exception("Preencha os campos <b>Nome do animal</b> e <b>Sexo do animal</b> para concluir o cadastro.");
            }
            if ($donoNaoDeclarado == 'N' && empty($cdPessoa)) {
                throw new Exception("Preencha a informação na aba <b>Proprietário do Animal</b> para concluir o cadastro.");
            }

            if($donoNaoDeclarado == 'N'){
                
                $dono = new \App\Models\Pessoas($nomePessoa, $select2cdCidade, $nrTelefone, '', $email, $nrCRMV, $select2cdBairro, $select2cdLogradouro, 'S', $cpf, $dataNascimento, $cdPessoa);

                if(empty($cdPessoa)){
                    $dono->Insert();
                } else {
                    if ($alterouPessoa == 'S'){
                        $dono->Update();
                    }
                }
                if(!$dono->getResult()){
                    throw new Exception($dono->getMessage());
                }
                $dono = $dono->getCodigo();
            } else {
                $dono = null;
            }


            $cad = new \App\Models\Animais($nome, $donoNaoDeclarado, $cdTipoAnimal, $cdEspecie, $cdRaca, $dsSexo, $idade, $anoNascimento, $dono, null, $codigo);
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
