<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Especies
{

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = "ESPECIES.CD_ESPECIE";
            if ($orderBy == 1) $orderBy = "ESPECIES.DESCRICAO";
            if ($orderBy == 2) $orderBy = "ESPECIES.FL_ATIVO";


            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaDescricao" => !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '',
                "pesquisaAtivo" => !empty($grid['columns'][2]['search']['value']) ? (int)$grid['columns'][2]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\Especies::SelectGrid($parametrosBusca);
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

    public static function controlar(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();

            $codigo = !empty($dadosForm['cdEspecie']) ? $dadosForm['cdEspecie'] : '';
            $descricao = !empty($dadosForm['especie']) ? $dadosForm['especie'] : '';
            $flativo = !empty($dadosForm['ativoEspecie']) ? (int)$dadosForm['ativoEspecie'] : '';

            if (empty($descricao) || empty($flativo) || $flativo > 2 ) {
                throw new Exception("Preencha os campos <b>Descrição</b>, <b>Tipo de Animal</b> e <b>Ativo</b> para concluir o cadastro.");
            }

            $cad = new \App\Models\Especies($descricao, $flativo, $codigo);
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

            
            $codigo = !empty($dadosForm['cdEspecie']) ? $dadosForm['cdEspecie'] : '';
            
            if (empty($codigo)) {
                throw new Exception("Houve um erro ao processo a requisição<br>Tente novamente mais tarde");
            }

            $cad = \App\Models\Especies::findById($codigo);
            $cad->Excluir();
            // $cad = new \App\Models\Especies(null, null, '', $codigo);


            if (!$cad->getResult()) {
                throw new Exception($cad->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => $codigo, "RETURN" => ''];
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
            $tipoAnimal = isset($dados['idTipoAnimal']) ? $dados['idTipoAnimal'] : '';

            if ($forSelect2) {
                $busca = new \App\Models\Especies('', '', '');

                $parametrosPesquisa = [
                    "colunas" => "CD_ESPECIE AS id, DESCRICAO AS text",
                    "descricaoPesquisa" => empty($descricao) ? '' : $descricao,
                    "TipoAnimal" => $tipoAnimal
                ];

                $busca->generalSearch($parametrosPesquisa);
            }

            if (!$busca->getResult()) {
                throw new Exception($busca->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $busca->getReturn()];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}
