<?php

namespace App\Views;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroEspecieModal
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

            $Especie = \App\Models\Especies::findById($idAlteracao);

            if (empty($Especie->getCodigo())) {
                $exibirExcluir = false;

                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_INSERIR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;
            } else {

                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_EDITAR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;

                $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_EXCLUIR');
                $exibirExcluir = $permissaoExcluir == true ? true : false;
            }

            $selectAtivo = '
            <div class="form-floating">
            <select class="form-select mb-3" id="ativoTipoAnimal" name="ativoEspecie" aria-label="Floating label select example">
              <option value="0">Selecione...</option>
              <option value="1" ' . ($Especie->getAtivo() == 1 ? 'selected' : "") . '>Sim</option>
              <option value="2" ' . ($Especie->getAtivo() == 0 ? 'selected' : "") . '>Não</option>
            </select>
            <label for="ativoEspecie">Ativo no Sistema</label>
          </div>';
        } catch (Exception $e) {
            return $this->twig->render($response, 'erroModal.twig', ["erro" => $e->getMessage()]);
        }

        return $this->twig->render(
            $response,
            'modalCadastroEspecie.twig',
            [
                "selectAtivo" => $selectAtivo,
                "codigo" => $Especie->getCodigo(),
                "descricao" => $Especie->getDescricao(),
                "exibirExcluir" => $exibirExcluir,
                "exibirSalvar" => $exibirSalvar
            ]
        );
    }
}
