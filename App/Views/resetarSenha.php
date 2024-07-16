<?php

namespace App\Views;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class resetarSenha
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {

        $userSessao = \App\Helpers\Sessao::getInfoSessao();
        $usuario = \App\Models\Usuarios::findById($userSessao['userid']);

        return $this->twig->render(
            $response,
            'resetarSenha.twig',
            ["usuarioConfirma" => $usuario->getUsuarioSistema()]
        );
    }
}
