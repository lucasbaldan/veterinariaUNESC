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

            $cdPessoa = !empty($Formulario['select2cdPessoa']) ? $Formulario['select2cdPessoa'] : '';
            $usuario = !empty($Formulario['dsUsuario']) ? $Formulario['dsUsuario'] : '';
            $cdGrupoUsuarios = !empty($Formulario['cdGrupoUsuarios']) ? $Formulario['cdGrupoUsuarios'] : '';
            $flAtivo = !empty($Formulario['flAtivo']) ? $Formulario['flAtivo'] : '';
            $cdUsuario = !empty($Formulario['cdUsuario']) ? $Formulario['cdUsuario'] : '';


            $dsSenha = !empty($Formulario['dsSenha']) ? $Formulario['dsSenha'] : '';
            $dsConfirmaSenha = !empty($Formulario['dsConfirmaSenha']) ? $Formulario['dsConfirmaSenha'] : '';

            if ($dsSenha === $dsConfirmaSenha) {
                $senha = $dsSenha;
            } else {
                throw new Exception("<b>Erro ao salvar o usuário</b><br><br> A senha e a confirmação da senha não são iguais.", 400);
                return;
            }
            if (strlen(trim($senha)) < 6) {
                throw new Exception("A senha precisa ter pelo menos 6 caracteres.", 400);
                return;
            }

            $usuarios = new \App\Models\Usuarios($cdPessoa, $usuario, $senha, $cdGrupoUsuarios, $flAtivo, $cdUsuario);

            if (empty($cdUsuario)) {
                $usuarios->Insert();
            } else {
                $usuarios->Update();
            }

            if (!$usuarios->GetResult()) {
                throw new Exception("<b>Erro ao salvar o usuário: " . $usuarios->GetMessage() . "</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $usuarios->GetReturn()];
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

    public static function efetuarLogin(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $usuario = !empty($Formulario['usuario']) ? $Formulario['usuario'] : '';
            $senha = !empty($Formulario['senha']) ? $Formulario['senha'] : '';


            $usuario = \App\Models\Usuarios::efetuarLogin($usuario, $senha);

            if (!empty($usuario->getCodigo())) {
                $arrayUsuario = [
                    "CD_USUARIO" => $usuario->getCodigo(),
                    "USERNAME" => $usuario->getPessoa()->getNome()
                ];
                \App\Helpers\Sessao::startSession($arrayUsuario);
            } else {
                throw new Exception("Dados de acesso inválidos <br><br>Verifique seu usuário e senha");
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

    public static function alterarSenha(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $senhaAtual = !empty($Formulario['senhaAtual']) ? $Formulario['senhaAtual'] : '';
            $novaSenha = !empty($Formulario['novaSenha']) ? $Formulario['novaSenha'] : '';
            $repitaSenha = !empty($Formulario['repitaSenha']) ? $Formulario['repitaSenha'] : '';
            $usuarioConfirma = !empty($Formulario['usuarioConfirma']) ? $Formulario['usuarioConfirma'] : '';

            if ($novaSenha !== $repitaSenha) throw new Exception('Repita a senha corretamente para continuar a operação');
            $usuario = \App\Models\Usuarios::efetuarLogin($usuarioConfirma, $senhaAtual);

            if (empty($usuario->getCodigo())) throw new Exception('Senha atual informado não corresponde aos dados de acesso válidos do usuário.');
            if(strlen(trim($novaSenha)) < 6) throw new Exception('A nova senha precisa ter pelo menos 6 caracteres');

            $usuario->setSenha($novaSenha);
            $usuario->Update();

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = "CD_USUARIO";
            if ($orderBy == 1) $orderBy = "NM_USUARIO";
            if ($orderBy == 2) $orderBy = "NM_GRUPO_USUARIOS";
            if ($orderBy == 3) $orderBy = "FL_ATIVO";

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaNmUsuario" => !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '',
                "pesquisaGrupoUsuario" => !empty($grid['columns'][2]['search']['value']) ? $grid['columns'][2]['search']['value'] : '',
                "pesquisaAtivo" => !empty($grid['columns'][3]['search']['value']) ? $grid['columns'][2]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\Usuarios::SelectGrid($parametrosBusca);
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
}
