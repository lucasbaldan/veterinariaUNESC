<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TiposAnimais {

    public static function retornaGrid(Request $request, Response $response){

        try{
            
        $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
        $codigoHTTP = 200;
        }
        catch(Exception $e){
        $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
        $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}