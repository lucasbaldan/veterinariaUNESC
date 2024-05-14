<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class FormularioLPV
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        // Renderiza a tela de login usando o Twig
        $formulario = $this->twig->fetch('formularioLPV.twig');

        return $this->twig->render($response, 'TelaBaseComMenus.twig', [
            'conteudo_tela' => $formulario,
        ]);
    }
}