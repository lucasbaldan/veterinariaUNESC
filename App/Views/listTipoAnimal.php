<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class listTipoAnimal
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        // Renderiza a tela de login usando o Twig
        $formulario = $this->twig->fetch('listTipoAnimal.twig');
        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $formulario]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'jsLinks' => 'listTipoAnimal.js',
            'cssLinks' => 'TelaMenus.css',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}