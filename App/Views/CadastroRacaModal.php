<?php

namespace App\Views;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroRacaModal
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

            $Raca = \App\Models\Raças::findById($idAlteracao);

            if (empty($Raca->getCodigo())) {
                $exibirExcluir = false;
                $selectEspecie = " ";

                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('RACA', 'FL_INSERIR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;
                
            } else {
                $selectEspecie = '<option value="' . $Raca->getEspecie()->getCodigo() . '" selected>' . $Raca->getEspecie()->getDescricao() . '</option>';

                $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('RACA', 'FL_EDITAR');
                $exibirSalvar = $permissaoSalvar == true ? true : false;

                $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('RACA', 'FL_EXCLUIR');
                $exibirExcluir = $permissaoExcluir == true ? true : false;
            }

            $selectAtivo = '
            <div class="form-floating">
            <select class="form-select mb-3" id="ativoRaca" name="ativoRaca" aria-label="Floating label select example">
              <option value="0">Selecione...</option>
              <option value="1" ' . ($Raca->getAtivo() == 1 ? 'selected' : "") . '>Sim</option>
              <option value="2" ' . ($Raca->getAtivo() == 0 ? 'selected' : "") . '>Não</option>
            </select>
            <label for="ativoRaca">Ativo no Sistema</label>
          </div>';
        } catch (Exception $e) {
            return $this->twig->render($response, 'erroModal.twig', ["erro" => $e->getMessage()]);
        }

        // $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('RACA', 'FL_EDITAR');
        // $exibirSalvar = $permissaoSalvar == true ? true : false;

        // $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('RACA', 'FL_EXCLUIR');
        // $exibirExcluir = $permissaoExcluir == true ? true : false;

        return $this->twig->render(
            $response,
            'modalCadastroRaca.twig',
            [
                "selectEspecie" => $selectEspecie,
                "selectAtivo" => $selectAtivo,
                "codigo" => $Raca->getCodigo(),
                "descricao" => $Raca->getDescricao(),
                "exibirExcluir" => $exibirExcluir,
                "exibirSalvar" => $exibirSalvar
            ]
        );
    }
}
