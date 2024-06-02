<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroPessoas
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $ajaxTela = $request->getParsedBody();

        $cdPessoa = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';

        if (!empty($cdPessoa)) {
            $dadosPessoa = \App\Models\Pessoas::RetornaDadosPessoa($cdPessoa);

            $pessoas = $this->twig->fetch('cadastroPessoas.twig', [
                'cdPessoa' => $dadosPessoa['CD_PESSOA'],
                'nmPessoa' => $dadosPessoa['NM_PESSOA'],
                'dsCidade' => $dadosPessoa['CIDADE'],
                'nrTelefone' => $dadosPessoa['NR_TELEFONE'],
                'dsEmail' => $dadosPessoa['DS_EMAIL'],
                'nrCRMV' => $dadosPessoa['NR_CRMV'],
            ]);
        } else {
            $pessoas = $this->twig->fetch('cadastroPessoas.twig');
        }

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $pessoas]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "cadastroPessoas.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
