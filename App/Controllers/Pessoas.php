<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Pessoas
{

    // ESSA FUNÇÃO É DOS USUÁRIOS, NÃO PESSOAS
    public static function efetuarLogin(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $login = !empty($Formulario['usuario']) ? $Formulario['usuario'] : '';
            $senha = !empty($Formulario['senha']) ? $Formulario['senha'] : '';

            if (empty($login) || empty($senha)) {
                throw new Exception("Preencha os campos Login e Senha.", 400);
            }

            $dadosUsuario = new \App\Models\Pessoas($login, $senha);
            $dadosUsuario = $dadosUsuario->verificarAcesso();

            if (!$dadosUsuario) {
                throw new Exception("<b>Usuário ou senha inválidos</b><br><br> Por favor verifique os dados de acesso e tente novamente.", 400);
            }



            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }


    public static function Salvar(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';
            $nmPessoa = !empty($Formulario['nmPessoa']) ? $Formulario['nmPessoa'] : '';
            $dsCidade = !empty($Formulario['dsCidade']) ? $Formulario['dsCidade'] : '';
            $nrTelefone = !empty($Formulario['nrTelefone']) ? $Formulario['nrTelefone'] : '';
            $dsEmail = !empty($Formulario['dsEmail']) ? $Formulario['dsEmail'] : '';
            $nrCRMV = !empty($Formulario['nrCRMV']) ? $Formulario['nrCRMV'] : '';

            // if (empty($login) || empty($senha)){
            //     throw new Exception("Preencha os campos Login e Senha.", 400);
            // }

            $usuario = new \App\Models\Pessoas('', '', $nmPessoa, $dsCidade, $nrTelefone, $dsEmail, $nrCRMV, $cdPessoa);
            if (empty($cdPessoa)) {
                $retorno = $usuario->Insert();
            } else {
                $retorno = $usuario->Update();
            }

            // if (!$retorno) {
            //     throw new Exception("<b>Erro ao salvar a pessoa</b><br><br> Por favor verifique os dados e tente novamente.", 400);
            // }

            if (!$usuario->GetResult()) {
                throw new Exception($usuario->GetMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $usuario->GetReturn()];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function RetornarPessoas(Request $request, Response $response)
    {

        try {

            $retorno = \App\Models\Pessoas::GeneralSearch('');

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar acessar os dados/b><br><br> Por favor, tente novamente.", 400);
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

    public static function RetornarDadosPessoa(Request $request, Response $response)
    {
        
        try {
            $Formulario = $request->getParsedBody();
            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';

            $retorno = \App\Models\Pessoas::RetornaDadosPessoa($cdPessoa);

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar os dados da pessoa</b><br><br> Por favor, tente novamente.", 400);
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

    public static function ApagarPessoa(Request $request, Response $response)
    {
        
        try {
            $Formulario = $request->getParsedBody();
            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';

            $retorno = \App\Models\Pessoas::Delete($cdPessoa);

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar a pessoa</b><br><br> Por favor, tente novamente.", 400);
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

    public static function AtualizarExclusaoPessoa(Request $request, Response $response)
    {
        
        try {
            $Formulario = $request->getParsedBody();
            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';
            $acao = !empty($Formulario['acao']) ? $Formulario['acao'] : '';

            $retorno = \App\Models\Pessoas::AtualizarExclusaoPessoa($cdPessoa, $acao);

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar a pessoa</b><br><br> Por favor, tente novamente.", 400);
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
