<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroUsuarios
{
    private $twig;
    private $TelaComMenus;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
        $this->TelaComMenus = \App\Views\TelaComMenus::getTelaComMenus($this->twig);
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $ajaxTela = $request->getParsedBody();

        $cdUsuario = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';
        $usuario = \App\Models\Usuarios::findById($cdUsuario);
        $exibeExcluir = true;
        $exibeSalvar = true;

        if (!empty($cdUsuario)) {

            $exibeSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_EDITAR');
            $exibeExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_EXCLUIR');

            $cdPessoa = '<option value="' . ($usuario->getPessoa()->getCodigo()) . '">' . ($usuario->getPessoa()->getNome()) . '</option>';
            $cdGrupoUsuarios = '<option value="' . ($usuario->getGrupoUsuario()->getCodigo()) . '">' . ($usuario->getGrupoUsuario()->getNome()) . '</option>';
        } else {
            $exibeSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_INSERIR');

            $cdPessoa = '';
            $cdGrupoUsuarios = '';
            $exibeExcluir = false;
        }

        // $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_EDITAR');
        // $exibeSalvar = $permissaoSalvar == true ? true : false;

        // $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_EXCLUIR');
        // $exibeExcluir = $permissaoExcluir == true ? true : false;

        $selectAtivoUsuario =  '<select name="flAtivo" id="flAtivo" class="form-select">
                                <option value="S" ' . ($usuario->getFlAtivo() == 'S' ? 'selected' : '') . '>Sim</option>
                                <option value="N" ' . ($usuario->getFlAtivo() == 'N' ? 'selected' : '') . '>Não</option>
                                </select>';

        $telaCadastroUsuarios = $this->twig->fetch('cadastroUsuarios.twig', [
            'cdUsuario' => $usuario->getCodigo(),
            'flAtivo' => $selectAtivoUsuario,
            'dsUsuario' => $usuario->getLogin(),
            'select2cdPessoa' => $cdPessoa,
            'cdGrupoUsuarios' => $cdGrupoUsuarios,

            "exibeExcluir" => $exibeExcluir,
            "exibeSalvar" => $exibeSalvar
        ]);

        $conteudoTela = $this->TelaComMenus->renderTelaComMenus($telaCadastroUsuarios);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "cadastroUsuarios.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
