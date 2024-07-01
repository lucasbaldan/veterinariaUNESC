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
            $flAtivo = !empty($Formulario['flAtivo']) ? $Formulario['flAtivo'] : '';


            $dadosGrupoUsuarios = new \App\Models\GruposUsuarios($nmGrupoUsuarios, '', $flAtivo, $cdGrupoUsuarios);
            
            if(empty($cdGrupoUsuarios)){
                $retorno = $dadosGrupoUsuarios->Insert();
            }else{
                $retorno = $dadosGrupoUsuarios->Update();
            }
            

            if (!$dadosGrupoUsuarios->GetResult()) {
                throw new Exception("<b>Erro ao salvar o grupo de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $dadosGrupoUsuarios->GetResult()];
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

    public static function MontarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = "grupos_usuarios.cd_grupo_usuarios";
            if ($orderBy == 1) $orderBy = "grupos_usuarios.nm_grupo_usuarios";
            if ($orderBy == 2) $orderBy = "grupos_usuarios.fl_ativo";

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaDescricao" => !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '',
                "pesquisaAtivo" => !empty($grid['columns'][2]['search']['value']) ? (int)$grid['columns'][2]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\GruposUsuarios::SelectGrid($parametrosBusca);
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
