<?php

namespace App\Views;

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

            if (!empty($idAlteracao)) {
                $TipoAnimal = new \App\Models\TipoAnimais(null, null, $idAlteracao);
                $TipoAnimal->findById();

                if(!$TipoAnimal->getResult()){
                    throw new Exception($TipoAnimal->getMessage());
                }
                $exibirExcluir = true;
            } else {
                $TipoAnimal = new \App\Models\TipoAnimais('', '', '');
                $exibirExcluir = false;
            }

            $select = '
            <div class="form-floating">
            <select class="form-select mb-3" id="ativoTipoAnimal" name="ativoTipoAnimal" aria-label="Floating label select example">
              <option value="0">Selecione...</option>
              <option value="1" ' . ($TipoAnimal->getAtivo() == 1 ? 'selected' : "") . '>Sim</option>
              <option value="2" ' . ($TipoAnimal->getAtivo() == 0 ? 'selected' : "") . '>Não</option>
            </select>
            <label for="ativoAnimal">Ativo no Sistema</label>
          </div>';

        } catch (Exception $e) {
            return $this->twig->render($response, 'erroModal.twig', ["erro" => $e->getMessage()]);
        }

        return $this->twig->render($response, 'modalCadastroTipoAnimal.twig', 
        [
            "select" => $select,
            "codigo" => $TipoAnimal->getCodigo(),
            "descricao" => $TipoAnimal->getDescricao(),
            "exibirExcluir" => $exibirExcluir,
            "exibirSalvar" => $exibirSalvar
        ]);
    }
}
