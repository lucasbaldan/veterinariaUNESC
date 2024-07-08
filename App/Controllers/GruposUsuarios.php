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

            if (empty($cdGrupoUsuarios)) {
                $retorno = $dadosGrupoUsuarios->Insert();
            } else {
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

            if (!$busca->getReturn()) {
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

    public static function GestaoAcessos(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdGrupoUsuarios = !empty($Formulario['cdGrupoUsuarios']) ? $Formulario['cdGrupoUsuarios'] : '';

            $flAcessarFichaLPV = !empty($Formulario['flAcessarFichaLPV']) ? $Formulario['flAcessarFichaLPV'] : '';
            $flEditarFichaLPV = !empty($Formulario['flEditarFichaLPV']) ? $Formulario['flEditarFichaLPV'] : '';
            $flInserirFichaLPV = !empty($Formulario['flInserirFichaLPV']) ? $Formulario['flInserirFichaLPV'] : '';
            $flExcluirFichaLPV = !empty($Formulario['flExcluirFichaLPV']) ? $Formulario['flExcluirFichaLPV'] : '';

            $flAcessarCadastroPessoas = !empty($Formulario['flAcessarCadastroPessoas']) ? $Formulario['flAcessarCadastroPessoas'] : '';
            $flEditarCadastroPessoas = !empty($Formulario['flEditarCadastroPessoas']) ? $Formulario['flEditarCadastroPessoas'] : '';
            $flInserirCadastroPessoas = !empty($Formulario['flInserirCadastroPessoas']) ? $Formulario['flInserirCadastroPessoas'] : '';
            $flExcluirCadastroPessoas = !empty($Formulario['flExcluirCadastroPessoas']) ? $Formulario['flExcluirCadastroPessoas'] : '';

            $flAcessarCadastroUsuarios = !empty($Formulario['flAcessarCadastroUsuarios']) ? $Formulario['flAcessarCadastroUsuarios'] : '';
            $flEditarCadastroUsuarios = !empty($Formulario['flEditarCadastroUsuarios']) ? $Formulario['flEditarCadastroUsuarios'] : '';
            $flInserirCadastroUsuarios = !empty($Formulario['flInserirCadastroUsuarios']) ? $Formulario['flInserirCadastroUsuarios'] : '';
            $flExcluirCadastroUsuarios = !empty($Formulario['flExcluirCadastroUsuarios']) ? $Formulario['flExcluirCadastroUsuarios'] : '';

            $flAcessarControleAcessos = !empty($Formulario['flAcessarControleAcessos']) ? $Formulario['flAcessarControleAcessos'] : '';
            $flEditarControleAcessos = !empty($Formulario['flEditarControleAcessos']) ? $Formulario['flEditarControleAcessos'] : '';
            $flInserirControleAcessos = !empty($Formulario['flInserirControleAcessos']) ? $Formulario['flInserirControleAcessos'] : '';
            $flExcluirControleAcessos = !empty($Formulario['flExcluirControleAcessos']) ? $Formulario['flExcluirControleAcessos'] : '';


            $data = [
                "FICHA_LPV" => [
                    "FL_ACESSAR" => $flAcessarFichaLPV ? "S" : "N",
                    "FL_EDITAR" => $flEditarFichaLPV ? "S" : "N",
                    "FL_INSERIR" => $flInserirFichaLPV ? "S" : "N",
                    "FL_EXCLUIR" => $flExcluirFichaLPV ? "S" : "N"
                ],
                "CADASTRO_PESSOAS" => [
                    "FL_ACESSAR" => $flAcessarCadastroPessoas ? "S" : "N",
                    "FL_EDITAR" => $flEditarCadastroPessoas ? "S" : "N",
                    "FL_INSERIR" => $flInserirCadastroPessoas ? "S" : "N",
                    "FL_EXCLUIR" => $flExcluirCadastroPessoas ? "S" : "N"
                ],
                "CADASTRO_USUARIOS" => [
                    "FL_ACESSAR" => $flAcessarCadastroUsuarios ? "S" : "N",
                    "FL_EDITAR" => $flEditarCadastroUsuarios ? "S" : "N",
                    "FL_INSERIR" => $flInserirCadastroUsuarios ? "S" : "N",
                    "FL_EXCLUIR" => $flExcluirCadastroUsuarios ? "S" : "N"
                ],
                "CONTROLE_ACESSOS" => [
                    "FL_ACESSAR" => $flAcessarControleAcessos ? "S" : "N",
                    "FL_EDITAR" => $flEditarControleAcessos ? "S" : "N",
                    "FL_INSERIR" => $flInserirControleAcessos ? "S" : "N",
                    "FL_EXCLUIR" => $flExcluirControleAcessos ? "S" : "N"
                ]
            ];

            $permissoes = json_encode($data);

            $retorno = \App\Models\GruposUsuarios::SalvarPermissoes($cdGrupoUsuarios, $permissoes);

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar acessar os dados do grupo de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => 'Permissões salvas com sucesso!', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function VerificaAcessos(Request $request, Response $response)
    {
        try {
            $Formulario = $request->getParsedBody();

            $cdUsuario = !empty($Formulario['cdUsuario']) ? $Formulario['cdUsuario'] : null;
            $parteSistema = !empty($Formulario['parteSistema']) ? $Formulario['parteSistema'] : null;
            $tpAcesso = !empty($Formulario['tpAcesso']) ? $Formulario['tpAcesso'] : null;

            if (is_null($cdUsuario) || is_null($parteSistema) || is_null($tpAcesso)) {
                throw new Exception("Parâmetros inválidos fornecidos.", 400);
            }

            $usuario = \App\Models\Usuarios::RetornaDadosUsuario($cdUsuario);
            if (!$usuario) {
                throw new Exception("Usuário não encontrado.", 404);
            }

            $retorno = \App\Models\GruposUsuarios::RetornaDadosGrupoUsuarios($usuario['CD_GRUPO_USUARIOS']);
            if (!$retorno) {
                throw new Exception("Erro ao tentar acessar as permissões do grupo de usuários. Por favor, tente novamente.", 400);
            }

            $permissoesArray = json_decode($retorno['PERMISSOES'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Erro ao decodificar as permissões do grupo de usuários.", 500);
            }

            $resultado = false;
            if (!empty($permissoesArray) && isset($permissoesArray[$parteSistema][$tpAcesso])) {
                $resultado = $permissoesArray[$parteSistema][$tpAcesso] === 'S';
            }

            $respostaServidor = ["RESULT" => true, "MESSAGE" => '', "RETURN" => $resultado];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => false, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode() ? $e->getCode() : 500;
        }

        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function VerificaAcessosSemRequisicao($parteSistema, $tpAcesso)
    {
        try {
            $cdUsuario = $_SESSION['userid'];

            if (is_null($cdUsuario) || is_null($parteSistema) || is_null($tpAcesso)) {
                throw new Exception("Parâmetros inválidos fornecidos.", 400);
            }

            $usuario = \App\Models\Usuarios::RetornaDadosUsuario($cdUsuario);
            if (!$usuario) {
                throw new Exception("Usuário não encontrado.", 404);
            }

            $retorno = \App\Models\GruposUsuarios::RetornaDadosGrupoUsuarios($usuario['CD_GRUPO_USUARIOS']);
            if (!$retorno) {
                throw new Exception("Erro ao tentar acessar as permissões do grupo de usuários. Por favor, tente novamente.", 400);
            }

            if(empty($retorno['PERMISSOES'])) {
                return false;
            }

            $permissoesArray = json_decode($retorno['PERMISSOES'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Erro ao decodificar as permissões do grupo de usuários.", 500);
            }

            if (!empty($permissoesArray) && isset($permissoesArray[$parteSistema][$tpAcesso])) {
                return $permissoesArray[$parteSistema][$tpAcesso] === 'S';
            }
    
            return false;

        } catch (Exception $e) {
            return $e->getMessage();
        }

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
