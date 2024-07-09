<?php

namespace App\Views;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroLogradouroModal
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {

        try {
            $exibirExcluir = true;
            $exibirSalvar = true;

            $ajaxTela = $request->getParsedBody();
            $idAlteracao = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';

            $Logradouro = \App\Models\Logradouros::findById($idAlteracao);

            if (empty($Logradouro->getCodigo())) {
                $exibirExcluir = false;

                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LOGRADOURO', 'FL_INSERIR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;
            } else {
                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LOGRADOURO', 'FL_EDITAR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;

                $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LOGRADOURO', 'FL_EXCLUIR');
                $exibirExcluir = $permissaoExcluir == true ? true : false;
            }

        } catch (Exception $e) {
            return $this->twig->render($response, 'erroModal.twig', ["erro" => $e->getMessage()]);
        }

        return $this->twig->render(
            $response,
            'modalCadastroLogradouro.twig',
            [
                "codigo" => $Logradouro->getCodigo(),
                "descricao" => $Logradouro->getNome(),
                "exibirExcluir" => $exibirExcluir,
                "exibirSalvar" => $exibirSalvar
            ]
        );
    }
}
