<?php

namespace App\Views;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroMunicipioModal
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

            $Municipio = \App\Models\Municipios::findById($idAlteracao);

            if (empty($Municipio->getCodigo())) {
                $exibirExcluir = false;
                $selectEstado = " ";

                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('MUNICIPIO', 'FL_INSERIR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;
            } else {
                $selectEstado = '<option value="' . $Municipio->getEstado()->getCodigoIbge() . '" selected>' . $Municipio->getEstado()->getNome() . '</option>';

                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('MUNICIPIO', 'FL_EDITAR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;
        
                $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('MUNICIPIO', 'FL_EXCLUIR');
                $exibirExcluir = $permissaoExcluir == true ? true : false;
            }

        } catch (Exception $e) {
            return $this->twig->render($response, 'erroModal.twig', ["erro" => $e->getMessage()]);
        }

        return $this->twig->render(
            $response,
            'modalCadastroMunicipio.twig',
            [
                "selectEstado" => $selectEstado,
                "codigo" => $Municipio->getCodigo(),
                "descricao" => $Municipio->getDescricao(),
                "exibirExcluir" => $exibirExcluir,
                "exibirSalvar" => $exibirSalvar
            ]
        );
    }
}
