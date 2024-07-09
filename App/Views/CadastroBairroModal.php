<?php

namespace App\Views;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroBairroModal
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

            $Bairro = \App\Models\Bairros::findById($idAlteracao);

            if (empty($Bairro->getCodigo())) {
                $exibirExcluir = false;

                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('BAIRRO', 'FL_INSERIR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;
            } else {
                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('BAIRRO', 'FL_EDITAR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;

                $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('BAIRRO', 'FL_EXCLUIR');
                $exibirExcluir = $permissaoExcluir == true ? true : false;
            }

        } catch (Exception $e) {
            return $this->twig->render($response, 'erroModal.twig', ["erro" => $e->getMessage()]);
        }

        return $this->twig->render(
            $response,
            'modalCadastroBairro.twig',
            [
                "codigo" => $Bairro->getCodigo(),
                "descricao" => $Bairro->getNome(),
                "exibirExcluir" => $exibirExcluir,
                "exibirSalvar" => $exibirSalvar
            ]
        );
    }
}
