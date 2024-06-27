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

    public static function General(Request $request, Response $response)
    {

        try {
            $dados = $request->getParsedBody();
            $forSelect2 = isset($dados['forSelect2']) ? $dados['forSelect2'] : '';

            // $retorno = \App\Models\GruposUsuarios::GeneralSearch('');
            if ($forSelect2) {
                $busca = new \App\Models\GruposUsuarios('', '', '', '');

                $parametrosPesquisa = [
                    "COLUNAS" => "CD_GRUPO_USUARIOS AS id, NM_GRUPO_USUARIOS AS text",
                    "descricaoPesquisa" => empty($descricao) ? '' : $descricao
                ];

                $busca->GeneralSearch($parametrosPesquisa);
                
            }

            if(!$busca->getReturn()){
                throw new Exception($busca->getMessage());
            }


            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $busca->getReturn()];
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
        
        try {
            $Formulario = $request->getParsedBody();
            $cdGrupoUsuarios = !empty($Formulario['cdGrupoUsuarios']) ? $Formulario['cdGrupoUsuarios'] : '';

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
