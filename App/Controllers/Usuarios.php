<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Usuarios
{

    public static function Salvar(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdUsuario = !empty($Formulario['cdUsuario']) ? $Formulario['cdUsuario'] : '';
            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';
            $usuario = !empty($Formulario['usuario']) ? $Formulario['usuario'] : '';
            $senha = !empty($Formulario['senha']) ? $Formulario['senha'] : '';
            $cdGrupoUsuarios = !empty($Formulario['cdGrupoUsuarios']) ? $Formulario['cdGrupoUsuarios'] : '';

            $usuarios = new \App\Models\Usuarios($cdPessoa, $usuario, $senha, $cdGrupoUsuarios, $cdUsuario);

            if (empty($cdUsuario)) {
                $retorno = $usuarios->Insert();
            } else {
                $retorno = $usuarios->Update();
            }

            if (!$retorno) {
                throw new Exception("<b>Erro ao salvar o usuário</b><br><br> Por favor, tente novamente.", 400);
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

    public static function ExcluirUsuario(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdUsuario = !empty($Formulario['cdUsuario']) ? $Formulario['cdUsuario'] : '';


            $retorno = \App\Models\Usuarios::Delete($cdUsuario);

            if (!$retorno) {
                throw new Exception("<b>Erro ao excluir o usuário</b><br><br> Por favor, tente novamente.", 400);
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

    public static function RetornarUsuarios(Request $request, Response $response)
    {

        try {

            $retorno = \App\Models\Usuarios::GeneralSearch();

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

    public static function RetornarDadosUsuario(Request $request, Response $response)
    {
        
        try {
            $Formulario = $request->getParsedBody();
            $cdUsuario = !empty($Formulario['cdUsuario']) ? $Formulario['cdUsuario'] : '';

            $retorno = \App\Models\Usuarios::RetornaDadosUsuario($cdUsuario);

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar acessar os dados do usuário</b><br><br> Por favor, tente novamente.", 400);
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

    public static function AtivarDesativarUsuario(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdUsuario = !empty($Formulario['cdUsuario']) ? $Formulario['cdUsuario'] : '';
            $acao = !empty($Formulario['acao']) ? $Formulario['acao'] : '';


            $retorno = \App\Models\Usuarios::AtivarDesativarUsuario($cdUsuario, $acao);

            if (!$retorno) {
                if ($acao == 'DESATIVAR'){
                    throw new Exception("<b>Erro ao desativar o usuário</b><br><br> Por favor, tente novamente.", 400);
                }else{
                    throw new Exception("<b>Erro ao ativar o usuário</b><br><br> Por favor, tente novamente.", 400);
                }
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
