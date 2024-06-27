<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroGruposUsuarios
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {

        $pessoas = $this->twig->fetch('gruposUsuarios.twig');
        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $pessoas]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "gruposUsuarios.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
