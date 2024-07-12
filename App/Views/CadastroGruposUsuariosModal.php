<?php

namespace App\Views;

use App\Models\TipoAnimais;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroGruposUsuariosModal
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
      $cdGrupoUsuarios = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';


      if (!empty($cdGrupoUsuarios)) {

        $exibirExcluir =  \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_GRUPOS_USUARIOS', 'FL_EXCLUIR');
        $exibirSalvar =  \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_GRUPOS_USUARIOS', 'FL_EDITAR');

        $grupoUsuarios = \App\Models\GruposUsuarios::findById($cdGrupoUsuarios);
        $codigo = $grupoUsuarios->getCodigo();
        $nome = $grupoUsuarios->GetNome();

        $select = '
                <div class="form-floating">
                <select class="form-select mb-3" id="flAtivo" name="flAtivo" aria-label="Floating label select example">
                  <option value="">Selecione...</option>
                  <option value="S" ' . ($grupoUsuarios->getAtivo() == 'S' ? 'selected' : "") . '>Sim</option>
                  <option value="N" ' . ($grupoUsuarios->getAtivo() == 'N' ? 'selected' : "") . '>Não</option>
                </select>
                <label for="flAtivo">Ativo no Sistema</label>
              </div>';
      } else {
        $exibirExcluir = false;
        $exibirSalvar =  \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_GRUPOS_USUARIOS', 'FL_INSERIR');
        $codigo = '';
        $nome = '';
        $select = '
                <div class="form-floating">
                <select class="form-select mb-3" id="flAtivo" name="flAtivo" aria-label="Floating label select example">
                  <option value="">Selecione...</option>
                  <option value="S">Sim</option>
                  <option value="N">Não</option>
                </select>
                <label for="flAtivo">Ativo no Sistema</label>
              </div>';
      }
    } catch (Exception $e) {
      return $this->twig->render($response, 'erroModal.twig', ["erro" => $e->getMessage()]);
    }

    return $this->twig->render(
      $response,
      'modalCadastroGrupoUsuarios.twig',
      [
        "select" => $select,
        "cdGrupoUsuarios" => $codigo,
        "nmGrupoUsuarios" => $nome,
        "exibirExcluir" => $exibirExcluir,
        "exibirSalvar" => $exibirSalvar
      ]
    );
  }
}
