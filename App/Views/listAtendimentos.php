<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class listAtendimentos
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $formulario = $this->twig->fetch('listAtendimentos.twig');
        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $formulario]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'jsLinks' => 'listAtendimentos.js',
            'cssLinks' => 'TelaMenus.css',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}