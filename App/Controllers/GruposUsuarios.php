<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GruposUsuarios
{

    public static function Salvar(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdGrupoUsuarios = !empty($Formulario['cdGrupoUsuarios']) ? $Formulario['cdGrupoUsuarios'] : '';
            $nmGrupoUsuarios = !empty($Formulario['nmGrupoUsuarios']) ? $Formulario['nmGrupoUsuarios'] : '';
            $flAcessar = !empty($Formulario['flAcessar']) ? $Formulario['flAcessar'] : '';
            $flEditar = !empty($Formulario['flEditar']) ? $Formulario['flEditar'] : '';
            $flExcluir = !empty($Formulario['flExcluir']) ? $Formulario['flExcluir'] : '';


            $dadosGrupoUsuarios = new \App\Models\GruposUsuarios($nmGrupoUsuarios, $flAcessar, $flEditar, $flExcluir, $cdGrupoUsuarios);
            
            if(empty($cdGrupoUsuarios)){
                $retorno = $dadosGrupoUsuarios->Insert();
            }else{
                $retorno = $dadosGrupoUsuarios->Update();
            }
            

            if (!$retorno) {
                throw new Exception("<b>Erro ao criar o grupo de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function ExcluirGruposUsuarios(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdGrupoUsuarios = !empty($Formulario['cdGrupoUsuarios']) ? $Formulario['cdGrupoUsuarios'] : '';


            $retorno = \App\Models\GruposUsuarios::Delete($cdGrupoUsuarios);

            if (!$retorno) {
                throw new Exception("<b>Erro ao excluir o grupo de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function RetornarGruposUsuarios(Request $request, Response $response)
    {

        try {

            $retorno = \App\Models\GruposUsuarios::GeneralSearch('');

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar acessar os grupos de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function RetornarDadosGrupoUsuario(Request $request, Response $response)
    {
        $cdGrupoUsuarios = !empty($Formulario['cdGrupoUsuarios']) ? $Formulario['cdGrupoUsuarios'] : '';

        try {

            $retorno = \App\Models\GruposUsuarios::RetornaDadosGrupoUsuarios($cdGrupoUsuarios);

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar acessar os dados do grupo de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}
