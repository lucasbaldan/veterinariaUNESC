<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroLaboratorios
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
        $permissao = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LABORATORIOS', 'FL_ACESSAR');
        if (!$permissao) {
            return $this->twig->render($response, 'TelaBase.twig', [
                'versao' => $GLOBALS['versao'],
                'cssLinks' => 'TelaMenus.css',
                'conteudo_tela' => $this->TelaComMenus->renderTelaComMenus($this->twig->fetch('telaErro.twig')),
            ]);
        }

        $ajaxTela = $request->getParsedBody();

        $idAlteracao = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';

        $exibeExcluir = true;
        $exibeSalvar = true;

        $laboratorio = \App\Models\Laboratorios::findById($idAlteracao);

        if (!empty($idAlteracao)) {
            $exibeSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LABORATORIOS', 'FL_EDITAR');
            $exibeExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LABORATORIOS', 'FL_EXCLUIR');
            $urlGaleria = $laboratorio->getLogoId();
        } else {
            $exibeExcluir = false;
            $exibeSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LABORATORIOS', 'FL_INSERIR');
            $urlGaleria = '';
        }


        $selectAtivoLaboratorio =  '<select name="ativo" id="ativo" class="form-select">
                        <option value="S" ' . ($laboratorio->getAtivo() == 'S' ? 'selected' : '') . '>Sim</option>
                        <option value="N" ' . ($laboratorio->getAtivo() == 'N' ? 'selected' : '') . '>Não</option>
                        </select>';


        $telaCadastroLaboratorios = $this->twig->fetch('cadastroLaboratorios.twig', [
            "cdLaboratorio" => $laboratorio->getCodigo(),
            "ativo" => $selectAtivoLaboratorio,
            "nmLaboratorio" => $laboratorio->getNome(),
            "selectAtivoLaboratorio" => $selectAtivoLaboratorio,
            "urlGaleria" => $urlGaleria,

            "exibeExcluir" => $exibeExcluir,
            "exibeSalvar" => $exibeSalvar,
            "exibeGaleria" => true
        ]);

        $conteudoTela = $this->TelaComMenus->renderTelaComMenus($telaCadastroLaboratorios);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => "TelaMenus.css;tabs.css",
            'jsLinks' => "cadastroLaboratorios.js;tabs.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
