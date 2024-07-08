<?php

namespace App\Views;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class recarregarGaleria
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {

        $post = $request->getParsedBody();
        $idFichaAlteracao = isset($post['cdAtendimento']) ? $post['cdAtendimento'] : '';
        $Ficha = \App\Models\Atendimentos::findById($idFichaAlteracao);
        $urlGaleria = $Ficha->getImagesIds();

        return $this->twig->render( $response, 'recarregarGaleria.twig', [
            "urlGaleria" => $urlGaleria
        ]);
    }
}
