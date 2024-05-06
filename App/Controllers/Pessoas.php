<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Pessoas {

    public static function exibir(Request $request, Response $response){
        $response->getBody()->write(json_encode(["mensagem" => "Sucesso!"]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}