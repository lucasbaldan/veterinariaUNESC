<?php

namespace App\Views;

use App\Models\TipoAnimais;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroTipoAnimalModal
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

                $TipoAnimal = \App\Models\TipoAnimais::findById($idAlteracao);

                // $exibirExcluir = empty($TipoAnimal->getCodigo()) ? false : true;

                if (empty($TipoAnimal->getCodigo())) {
                    $exibirExcluir = false;
    
                    $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('TIPO_ANIMAL', 'FL_INSERIR');
                    $exibirSalvar = $permissaoSalvar == true ? true : false;
                } else {
                    $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('TIPO_ANIMAL', 'FL_EDITAR');
                    $exibirSalvar = $permissaoSalvar == true ? true : false;
    
                    $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('TIPO_ANIMAL', 'FL_EXCLUIR');
                    $exibirExcluir = $permissaoExcluir == true ? true : false;
                }
            

            $select = '
            <div class="form-floating">
            <select class="form-select mb-3" id="flAtivo" name="flAtivo" aria-label="Floating label select example">
              <option value="0">Selecione...</option>
              <option value="1" ' . ($TipoAnimal->getAtivo() == 1 ? 'selected' : "") . '>Sim</option>
              <option value="2" ' . ($TipoAnimal->getAtivo() == 0 ? 'selected' : "") . '>Não</option>
            </select>
            <label for="flAtivo">Ativo no Sistema</label>
          </div>';

        } catch (Exception $e) {
            return $this->twig->render($response, 'erroModal.twig', ["erro" => $e->getMessage()]);
        }

        return $this->twig->render($response, 'modalCadastroGrupoUsuarios.twig', 
        [
            "select" => $select,
            "codigo" => $TipoAnimal->getCodigo(),
            "descricao" => $TipoAnimal->getDescricao(),
            "exibirExcluir" => $exibirExcluir,
            "exibirSalvar" => $exibirSalvar
        ]);
    }
}
