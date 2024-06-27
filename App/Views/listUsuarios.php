<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ListUsuarios
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $formulario = $this->twig->fetch('listUsuarios.twig');
        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $formulario]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'jsLinks' => 'listUsuarios.js',
            'cssLinks' => 'TelaMenus.css',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}