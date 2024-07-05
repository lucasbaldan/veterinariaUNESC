<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroUsuarios
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $ajaxTela = $request->getParsedBody();

        $cdUsuario = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';
        $usuario = \App\Models\Usuarios::findById($cdUsuario);
        $exibeExcluir = true;
        $exibeSalvar = true;

        if (!empty($cdUsuario)) {
            $cdPessoa = '<option value="' . ($usuario->getPessoa()->getCodigo()) . '">' . ($usuario->getPessoa()->getNome()) . '</option>';
            $cdGrupoUsuarios = '<option value="' . ($usuario->getGrupoUsuario()->getCodigo()) . '">' . ($usuario->getGrupoUsuario()->getNome()) . '</option>';
        } else {
            $cdPessoa = '';
            $cdGrupoUsuarios = '';
            $exibeExcluir = false;
        }

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

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $telaCadastroUsuarios]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "cadastroUsuarios.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
