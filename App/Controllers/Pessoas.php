<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Pessoas
{

   public static function Salvar(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';
            $nmPessoa = !empty($Formulario['nmPessoa']) ? $Formulario['nmPessoa'] : '';
            $cpf = !empty($Formulario['cpfPessoa']) ? $Formulario['cpfPessoa'] : '';
            $dataNascimento = !empty($Formulario['dataNascimento']) ? $Formulario['dataNascimento'] : '';
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


            $pessoa = new \App\Models\Pessoas($nmPessoa, $cdCidade, $nrTelefone, '', $dsEmail, $nrCRMV, $cdBairro, $cdLogradouro, $ativo, $cpf, $dataNascimento, $cdPessoa);
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
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function retornaPesquisaModal(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $nmPessoa = !empty($Formulario['nmPessoaModal']) ? $Formulario['nmPessoaModal'] : '';
            $cdCidade = !empty($Formulario['cdCidadeModal']) ? $Formulario['cdCidadeModal'] : '';
            $telefone = !empty($Formulario['telefoneCelularModal']) ? $Formulario['telefoneCelularModal'] : '';

            $arrayParam = [
                "COLUNAS" => "PESSOAS.CD_PESSOA, PESSOAS.NM_PESSOA, PESSOAS.NR_TELEFONE, CIDADES.NOME AS CIDADE",
                "NM_PESSOA" => $nmPessoa,
                "ID_CIDADE" => $cdCidade,
                "TELEFONE" => $telefone
            ];

            $retorno = new \App\Models\Pessoas('', '', '', '', '', '', '', '', '', '', '');
            $retorno->GeneralSearch($arrayParam);

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno->getReturn()];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function RetornarDadosPessoa(Request $request, Response $response)
    {
        
        try {
            $Formulario = $request->getParsedBody();
            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';

            $pessoa = \App\Models\Pessoas::findById($cdPessoa);

            if (empty($pessoa->getCodigo())) {
                throw new Exception("<b>Erro ao tentar localizar os dados da pessoa</b><br><br> Por favor, tente novamente.", 400);
            }

            $retorno = [
                'nm_pessoa' => $pessoa->getNome(),
                'cd_cidade' => $pessoa->getCidade()->getCodigo(),
                'nm_cidade' => $pessoa->getCidade()->getDescricao(),
                'nr_telefone' => $pessoa->getTelefone(),
                'ds_email' => $pessoa->getEmail(),
                'nr_crmv' => $pessoa->getNrCRMV(),
                'cd_bairro' => $pessoa->getBairro()->getCodigo(),
                'nm_bairro' => $pessoa->getBairro()->getNome(),
                'cd_logradouro' => $pessoa->getLogradouro()->getCodigo(),
                'nm_logradouro' => $pessoa->getLogradouro()->getNome(),
                'fl_ativo' => $pessoa->getAtivo(),
                'cpf' => $pessoa->getCPF(),
                'data_nascimento' => $pessoa->getDataNascimento(),
                'cd_pessoa' => $pessoa->getCodigo()
            ];

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function ApagarPessoa(Request $request, Response $response)
    {
        
        try {
            $Formulario = $request->getParsedBody();
            $cdPessoa = !empty($Formulario['cdPessoa']) ? $Formulario['cdPessoa'] : '';

            $pessoa = new \App\Models\Pessoas('', '','','','','','','','', '', '', $cdPessoa);
            $pessoa->Delete();

            if (!$pessoa->getResult()) {
                throw new Exception("<b>Erro ao tentar processar requisição</b><br><br> " . $pessoa->getMessage(), 500);
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
            if ($orderBy == 0) $orderBy = "PESSOAS.CD_PESSOA";
            if ($orderBy == 1) $orderBy = "PESSOAS.NM_PESSOA";
            if ($orderBy == 2) $orderBy = "PESSOAS.FL_ATIVO";

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
                "recordsTotal" => isset($dadosSelect[0]['TOTAL_TABLE']) ? $dadosSelect[0]['TOTAL_TABLE'] : 0,
                "recordsFiltered" => isset($dadosSelect[0]['TOTAL_FILTERED']) ? $dadosSelect[0]['TOTAL_FILTERED'] : 0,
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

    public static function General(Request $request, Response $response)
    {

        try {
            
            $dados = $request->getParsedBody();

            $forSelect2 = isset($dados['forSelect2']) ? $dados['forSelect2'] : '';
            $descricao = isset($dados['buscaSelect2']) ? $dados['buscaSelect2'] : '';

            if ($forSelect2) {
                $busca = new \App\Models\Pessoas('', '', '', '', '', '', '', '', '', '', '');

                $parametrosPesquisa = [
                    "COLUNAS" => "PESSOAS.CD_PESSOA AS id, PESSOAS.NM_PESSOA AS text",
                    "NM_PESSOA" => empty($descricao) ? '' : $descricao
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
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}
