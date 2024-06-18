<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroAnimais
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $ajaxTela = $request->getParsedBody();

        //$cdPessoa = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';

        $Cadastro = $this->twig->fetch('cadastroAnimais.twig');
        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $Cadastro]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'cssLinks' => 'TelaMenus.css',
            'jsLinks' => 'cadastroAnimais.js',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
