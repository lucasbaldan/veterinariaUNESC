<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class listEspecie
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
        $permissao = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('ESPECIE', 'FL_ACESSAR');
        if (!$permissao) {
            return $this->twig->render($response, 'TelaBase.twig', [
                'versao' => $GLOBALS['versao'],
                'cssLinks' => 'TelaMenus.css',
                'conteudo_tela' => $this->TelaComMenus->renderTelaComMenus($this->twig->fetch('telaErro.twig')),
            ]);
        }

        $formulario = $this->twig->fetch('listEspecie.twig');
        $conteudoTela = $this->TelaComMenus->renderTelaComMenus($formulario);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'jsLinks' => 'listEspecie.js',
            'cssLinks' => 'TelaMenus.css',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}