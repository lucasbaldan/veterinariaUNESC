<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Pessoas {

    public static function efetuarLogin(Request $request, Response $response){

        try{
        $Formulario = $request->getParsedBody();

        $login = !empty($Formulario['usuario']) ? $Formulario['usuario'] : '';
        $senha = !empty($Formulario['senha']) ? $Formulario['senha'] : '';

        if (empty($login) || empty($senha)){
            throw new Exception("Preencha os campos Login e Senha.", 400);
        }

        $dadosUsuario = new \App\Models\Pessoas($login, $senha);
        $dadosUsuario = $dadosUsuario->verificarAcesso();

        if(!$dadosUsuario){
            throw new Exception("<b>Usuário ou senha inválidos</b><br><br> Por favor verifique os dados de acesso e tente novamente.", 400);  
        }

        $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $dadosUsuario];
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