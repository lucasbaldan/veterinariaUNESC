<?php

namespace App\Helpers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ImportadorAPI{

    public static function EstadosBrasileiros(Request $request, Response $response){

        try{       
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://servicodados.ibge.gov.br/api/v1/localidades/estados');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $listaEstados = curl_exec($ch);
        curl_close($ch);

        $conn = \App\Conn\Conn::getConn();
        $insert = new \App\Conn\Insert($conn);

        $listaEstados = json_decode($listaEstados, true);
        foreach($listaEstados as $estado){
            $insert->ExeInsert("ESTADOS", ["nome" => $estado['nome'],
                                           "UF" => $estado['sigla'],
                                           "cd_ibge" => $estado['id'],
                                           "desc_regiao_geografica" => $estado['regiao']['nome'],
                                           "cd_pais" => 1]);
        }

        $insert->Commit();
        $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
        $codigoHTTP = 200;
    }catch(Exception $e){
        $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
        $codigoHTTP = 500;
        $insert->Rollback();
    }
    $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
    return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');

    }

    public static function CidadesBrasileiras(Request $request, Response $response){

        try{       
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://servicodados.ibge.gov.br/api/v1/localidades/municipios');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $listaCidades = curl_exec($ch);
        curl_close($ch);

        $conn = \App\Conn\Conn::getConn();
        $insert = new \App\Conn\Insert($conn);

        $listaCidades = json_decode($listaCidades, true);
        foreach($listaCidades as $cidade){
            $insert->ExeInsert("CIDADES", ["nome" => $cidade['nome'],
                                           "id_ibge" => $cidade['id'],
                                           "ib_ibge_estado" => $cidade['regiao-imediata']['regiao-intermediaria']['UF']['id']
                                        ]);
        }

        $insert->Commit();
        $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
        $codigoHTTP = 200;
    }catch(Exception $e){
        $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
        $codigoHTTP = 500;
        $insert->Rollback();
    }
    $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
    return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');

    }
}