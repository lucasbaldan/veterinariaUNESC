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
            if ($orderBy == 0) $orderBy = "ANIMAIS.CD_ANIMAL";
            if ($orderBy == 1) $orderBy = "ANIMAIS.NM_ANIMAL";
            if ($orderBy == 2) $orderBy = "ANIMAIS.CD_ANIMAL";
            if ($orderBy == 3) $orderBy = "ESPECIES.DESCRICAO";
            if ($orderBy == 4) $orderBy = "RACAS.DESCRICAO";

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
                "recordsTotal" => isset($dadosSelect[0]['TOTAL_TABLE']) ? $dadosSelect[0]['TOTAL_TABLE'] : 0,
                "recordsFiltered" => isset($dadosSelect[0]['TOTAL_FILTERED']) ? $dadosSelect[0]['TOTAL_FILTERED'] : 0,
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

    public static function retornaPesquisaModal(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $nmAnimal = !empty($Formulario['nmAnimalModal']) ? $Formulario['nmAnimalModal'] : '';
            $especie = !empty($Formulario['especieModal']) ? $Formulario['especieModal'] : '';
            $raca = !empty($Formulario['racaModal']) ? $Formulario['racaModal'] : '';
            $dono = !empty($Formulario['donoAnimalModal']) ? $Formulario['donoAnimalModal'] : '';

            $arrayParam = [
                "COLUNAS" => "ANIMAIS.CD_ANIMAL, ANIMAIS.NM_ANIMAL, DONO.NM_PESSOA, ESPECIES.DESCRICAO AS ESPECIE, RACAS.DESCRICAO AS RACA",
                "NM_ANIMAL" => $nmAnimal,
                "ESPECIE" => $especie,
                "RACA" => $raca,
                "DONO" => $dono
            ];

            $retorno = \App\Models\Animais::GeneralSearch($arrayParam);

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
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

            //ENTRADAS DADOS ANIMAL
            $codigo = !empty($dadosForm['cdAnimal']) ? $dadosForm['cdAnimal'] : '';
            $nome = !empty($dadosForm['animal']) ? $dadosForm['animal'] : '';
            $cdEspecie = isset($dadosForm['select2especieAnimal']) ? $dadosForm['select2especieAnimal'] : '';
            $cdRaca = isset($dadosForm['select2racaAnimal']) ? $dadosForm['select2racaAnimal'] : '';
            $dsSexo = !empty($dadosForm['dsSexo']) ? $dadosForm['dsSexo'] : '';
            $flCastrado = !empty($dadosForm['flCastrado']) ? $dadosForm['flCastrado'] : '';

            // ENTRADAS DA PESSOA TUTORA DO ANIMAL
            $tutorNaoDeclarado = isset($dadosForm['tutorNaoDeclarado']) ? 'S' : 'N';
            $cdPessoa = !empty($dadosForm['cdPessoa']) ? $dadosForm['cdPessoa'] : '';
            $alterouPessoa = isset($dadosForm['alterouPessoa']) ? $dadosForm['alterouPessoa'] : '';
            $nomePessoa = isset($dadosForm['nmPessoa']) ? $dadosForm['nmPessoa'] : '';
            $nrTelefone = isset($dadosForm['nrTelefone']) ? $dadosForm['nrTelefone'] : '';
            $select2cdCidade = isset($dadosForm['select2cdCidade']) ? $dadosForm['select2cdCidade'] : '';
            $select2cdBairro = isset($dadosForm['select2cdBairro']) ? $dadosForm['select2cdBairro'] : '';
            $select2cdLogradouro = isset($dadosForm['select2cdLogradouro']) ? $dadosForm['select2cdLogradouro'] : '';

            if (empty($nome) || empty($dsSexo)) {
                throw new Exception("Preencha os campos <b>Nome do animal</b> e <b>Sexo do animal</b> para concluir o cadastro.");
            }
            if ($tutorNaoDeclarado == 'N' && empty($cdPessoa) && empty($nomePessoa)) {
                throw new Exception("Preencha a informação na aba <b>Tutor do Animal</b> para concluir o cadastro.");
            }

            if ($tutorNaoDeclarado == 'N') {

                $tutor = \App\Models\Pessoas::findById($cdPessoa);

                $tutor->setNome($nomePessoa);
                $tutor->setTelefone($nrTelefone);
                $tutor->setCidade($select2cdCidade);
                $tutor->setBairro($select2cdBairro);
                $tutor->setLogradouro($select2cdLogradouro);
                $tutor->setAtivo('S');

                // $tutor->Update();

                // $tutor = new \App\Models\Pessoas($nomePessoa, $select2cdCidade, $nrTelefone, '', $email, $nrCRMV, $select2cdBairro, $select2cdLogradouro, 'S', $cpf, $dataNascimento, $cdPessoa);

                if (empty($cdPessoa)) {
                    $tutor->Insert();
                } else {
                    if ($alterouPessoa == 'S') {
                        $tutor->Update();
                    }
                }
                if (!$tutor->getResult()) {
                    throw new Exception($tutor->getMessage());
                }
                $tutor = $tutor->getCodigo();
            } else {
                $tutor = null;
            }


            $cad = new \App\Models\Animais($nome, $tutorNaoDeclarado, $cdEspecie, $cdRaca, $dsSexo, $flCastrado, $tutor, $codigo);
            if (empty($codigo)) {
                $cad->Inserir();
            } else {
                $cad->Atualizar();
            }

            if (!$cad->getResult()) {
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

            $cad = new \App\Models\Animais('', '', '', '', '', '', '', $codigo);
            $cad->Excluir();


            if (!$cad->getResult()) {
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

    public static function general(Request $request, Response $response)
    {
        try {

            $dados = $request->getParsedBody();

            $forSelect2 = isset($dados['forSelect2']) ? $dados['forSelect2'] : '';
            $descricao = isset($dados['buscaSelect2']) ? $dados['buscaSelect2'] : '';

            if ($forSelect2) {
                $busca = new \App\Models\Animais('', '', '', '', '', '', '', '', '');

                $parametrosPesquisa = [
                    "COLUNAS" => "cd_animal AS id, nm_animal AS text",
                    "descricaoPesquisa" => empty($descricao) ? '' : $descricao,
                ];

                $retorno = $busca->generalSearch($parametrosPesquisa);
            }

            if (empty($retorno)) {
                throw new Exception($busca->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}
