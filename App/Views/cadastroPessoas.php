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

        $idAlteracao = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';

        $exibeExcluir = true;
        $exibeSalvar = true;
        
        $pessoa = \App\Models\Pessoas::findById($idAlteracao);

        if(!empty($idAlteracao)){
            $selectCidade = '<option value="'.($pessoa->getCidade()->getCodigo()).'">'.($pessoa->getCidade()->getDescricao()).'</option>';
            $selectBairro = '<option value="'.($pessoa->getBairro()->getCodigo()).'">'.($pessoa->getBairro()->getNome()).'</option>';
            $selectLogradouro = '<option value="'.($pessoa->getLogradouro()->getCodigo()).'">'.($pessoa->getLogradouro()->getNome()).'</option>';   
        } else {
            $exibeExcluir = false;
            $selectCidade = "";
            $selectBairro = "";
            $selectLogradouro = "";
        }

        $selectAtivo =  '<select name="AtivoPessoa" id="AtivoPessoa" class="form-select">
                        <option value="S" '.($pessoa->getAtivo() == 'S' ? 'selected' : '').'>Sim</option>
                        <option value="N" '.($pessoa->getAtivo() == 'N' ? 'selected' : '').'>Não</option>
                        </select>';

        
        $telaCadastroPessoa = $this->twig->fetch('cadastroPessoas.twig', [
            "cdPessoa" => $pessoa->getCodigo(),
            "selectAtivoPessoa" => $selectAtivo,
            "nmPessoa" => $pessoa->getNome(),
            "cpfPessoa" => $pessoa->getCPF(),
            "dataNascimento" => $pessoa->getDataNascimento(),
            "nrTelefone" => $pessoa->getTelefone(),
            "dsEmail" => $pessoa->getEmail(),
            "nrCRMV" => $pessoa->getNrCRMV(),
            "selectCidadePessoa" => $selectCidade,
            "selectBairroPessoa" => $selectBairro,
            "selectLogradouroPessoa" => $selectLogradouro,

            "exibeExcluir" => $exibeExcluir,
            "exibeSalvar" => $exibeSalvar
        ]);

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $telaCadastroPessoa]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "cadastroPessoas.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
