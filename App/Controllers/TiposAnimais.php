<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TiposAnimais
{

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $parametrosBusca = [
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" => $grid['order'][0]['column'],
                "orderAscDesc" => $grid['order'][0]['dir']
            ];

            $versaoTabela = $grid['draw'] > 1 ? $grid['draw'] : 1;


            $dadosSelect = \App\Models\TipoAnimais::SelectGrid($parametrosBusca);
            $dados = [
                "draw" => $versaoTabela,
                "recordsTotal" => $dadosSelect[1],
                "recordsFiltered" => $dadosSelect[1],
                "data" => $dadosSelect[0]
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
}
