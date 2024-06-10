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
            if ($orderBy == 0) $orderBy = "especies.cd_especie";
            if ($orderBy == 1) $orderBy = "especies.descricao";
            if ($orderBy == 2) $orderBy = "especies.fl_ativo";

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaDescricao" => !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '',
                "pesquisaTipoAnimal" => !empty($grid['columns'][2]['search']['value']) ? $grid['columns'][2]['search']['value'] : '',
                "pesquisaAtivo" => !empty($grid['columns'][3]['search']['value']) ? (int)$grid['columns'][3]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\Especies::SelectGrid($parametrosBusca);
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

            $codigo = !empty($dadosForm['cdEspecie']) ? $dadosForm['cdEspecie'] : '';
            $descricao = !empty($dadosForm['especie']) ? $dadosForm['especie'] : '';
            $cdTipoAnimal = !empty($dadosForm['select2cdTipoAnimal']) ? $dadosForm['select2cdTipoAnimal'] : '';
            $flativo = !empty($dadosForm['ativoEspecie']) ? (int)$dadosForm['ativoEspecie'] : '';

            if (empty($descricao) || empty($flativo) || $flativo > 2 || empty($cdTipoAnimal)) {
                throw new Exception("Preencha os campos <b>Descrição</b>, <b>Tipo de Animal</b> e <b>Ativo</b> para concluir o cadastro.");
            }

            $cad = new \App\Models\Especies($descricao, $flativo, $cdTipoAnimal, $codigo);
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

            $codigo = !empty($dadosForm['cdEspecie']) ? $dadosForm['cdEspecie'] : '';

            if (empty($codigo)) {
                throw new Exception("Houve um erro ao processo a requisição<br>Tente novamente mais tarde");
            }

            $cad = new \App\Models\Especies(null, null, '', $codigo);
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
