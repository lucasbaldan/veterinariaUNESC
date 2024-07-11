<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ListAcessosGruposUsuarios
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

        $pessoas = $this->twig->fetch('listAcessosGruposUsuarios.twig');
        $conteudoTela = $this->TelaComMenus->renderTelaComMenus($pessoas);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "listAcessosGruposUsuarios.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
