<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Pessoas
{

    // ESSA FUNÇÃO É DOS USUÁRIOS, NÃO PESSOAS

    // COM CERTEZA MEU AMIGO, SÓ ESTAVA TESTANDO E FUNCIONOUUUUUUU!
    // public static function efetuarLogin(Request $request, Response $response)
    // {

    //     try {
    //         $Formulario = $request->getParsedBody();

    //         $login = !empty($Formulario['usuario']) ? $Formulario['usuario'] : '';
    //         $senha = !empty($Formulario['senha']) ? $Formulario['senha'] : '';

    //         if (empty($login) || empty($senha)) {
    //             throw new Exception("Preencha os campos Login e Senha.", 400);
    //         }

    //         $dadosUsuario = new \App\Models\Pessoas($login, $senha);
    //         $dadosUsuario = $dadosUsuario->verificarAcesso();

    //         if (!$dadosUsuario) {
    //             throw new Exception("<b>Usuário ou senha inválidos</b><br><br> Por favor verifique os dados de acesso e tente novamente.", 400);
    //         }



    //         $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
    //         $codigoHTTP = 200;
    //     } catch (Exception $e) {
    //         $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
    //         $codigoHTTP = $e->getCode();
    //     }
    //     $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
    //     return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    // }


    public static function Salvar(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';
            $nmPessoa = !empty($Formulario['nmPessoa']) ? $Formulario['nmPessoa'] : '';
            $nrTelefone = !empty($Formulario['nrTelefone']) ? $Formulario['nrTelefone'] : '';
            $dsEmail = !empty($Formulario['dsEmail']) ? $Formulario['dsEmail'] : '';
            $nrCRMV = !empty($Formulario['nrCRMV']) ? $Formulario['nrCRMV'] : '';
            $dsCidade = !empty($Formulario['dsCidade']) ? $Formulario['dsCidade'] : '';
            $cdCidade = !empty($Formulario['select2cdCidade']) ? $Formulario['select2cdCidade'] : '';
            $cdBairro = !empty($Formulario['select2cdBairro']) ? $Formulario['select2cdBairro'] : '';
            $cdLogradouro = !empty($Formulario['select2cdLogradouro']) ? $Formulario['select2cdLogradouro'] : '';
            $ativo = !empty($Formulario['AtivoPessoa']) ? $Formulario['AtivoPessoa'] : '';

            if(empty($nmPessoa)){
                throw new Exception("Preencha o campo <b>Nome</b> para concluir o cadastro.");
            }


            $pessoa = new \App\Models\Pessoas($nmPessoa, $cdCidade, $nrTelefone, '', $dsEmail, $nrCRMV, $cdBairro, $cdLogradouro, $ativo, $cdPessoa);
            if (empty($cdPessoa)) {
                $pessoa->Insert();
            } else {
                $pessoa->Update();
            }

            if (!$pessoa->GetResult()) {
                throw new Exception($pessoa->getMessage());
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

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = "pessoas.CD_PESSOA";
            if ($orderBy == 1) $orderBy = "pessoas.NM_PESSOA";
            if ($orderBy == 2) $orderBy = "pessoas.FL_EXCLUIDO";

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaDescricao" => !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '',
                "pesquisaAtivo" => !empty($grid['columns'][2]['search']['value']) ? $grid['columns'][2]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\Pessoas::SelectGrid($parametrosBusca);
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
