<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroAnimais
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
        $ajaxTela = $request->getParsedBody();

        $exibeSalvar = true;
        $exibeExcluir = true;

        $idAlteracao = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';

        $Animal = \App\Models\Animais::findById($idAlteracao);

        if (!empty($Animal->getCodigo())) {
            $exibeSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('ANIMAL', 'FL_EDITAR');
            $exibeExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('ANIMAL', 'FL_EXCLUIR');

            $selectEspecie = '<option value="' . $Animal->getEspecie()->getCodigo() . '" selected>' . $Animal->getEspecie()->getDescricao() . '</option>';
            $selectRaca = '<option value="' . $Animal->getRaca()->getCodigo() . '" selected>' . $Animal->getRaca()->getDescricao() . '</option>';

            if (!empty($Animal->getDono1()->getCodigo())) {
                $selectCidadePessoa = '<option value="' . $Animal->getDono1()->getCidade()->getCodigo() . '" selected>' . $Animal->getDono1()->getCidade()->getDescricao() . '</option>';
                $selectBairroPessoa = '<option value="' . $Animal->getDono1()->getBairro()->getCodigo() . '" selected>' . $Animal->getDono1()->getBairro()->getNome() . '</option>';
                $selectLogradouroPessoa = '<option value="' . $Animal->getDono1()->getLogradouro()->getCodigo() . '" selected>' . $Animal->getDono1()->getLogradouro()->getNome() . '</option>';
            } else {
                $selectBairroPessoa = '';
                $selectCidadePessoa = '';
                $selectLogradouroPessoa = '';
            }
        } else {
            $exibeSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('ANIMAL', 'FL_INSERIR');
            
            $exibeExcluir = false;
            $selectEspecie = '';
            $selectRaca = '';
            $selectBairroPessoa = '';
            $selectCidadePessoa = '';
            $selectLogradouroPessoa = '';
        }

        $selectSexoAnimal = '<select class="form-select" id="dsSexo" name="dsSexo" aria-label="Sexo Animal">
                                    <option value="">Selecione...</option>
                                    <option value="M" ' . ($Animal->getSexo() == 'M' ? 'selected' : ' ') . '>Macho</option>
                                    <option value="F" ' . ($Animal->getSexo() == 'F' ? 'selected' : ' ') . '>Fêmea</option>
                                </select>';

        $selectFlCastrado = '<select class="form-select" id="flCastrado" name="flCastrado" aria-label="Animal Castrado?">
                                    <option value="N" ' . ($Animal->getFlCastrado() == 'N' ? 'selected' : ' ') . '>Não</option>
                                    <option value="S" ' . ($Animal->getFlCastrado() == 'S' ? 'selected' : ' ') . '>Sim</option>
                                </select>';


        $Cadastro = $this->twig->fetch('cadastroAnimais.twig', [
            "cdAnimal" => $Animal->getCodigo(),
            "animal" => $Animal->getNome(),
            "selectEspecieAnimal" => $selectEspecie,
            "selectRacaAnimal" => $selectRaca,
            "selectSexoAnimal" => $selectSexoAnimal,
            "selectFlCastrado" => $selectFlCastrado,

            "cdPessoa" => $Animal->getDono1()->getCodigo(),
            "nmPessoa" => $Animal->getDono1()->getNome(),
            "cpfPessoa" => $Animal->getDono1()->getCPF(),
            "dataNascimento" => $Animal->getDono1()->getDataNascimento(),
            "nrTelefone" => $Animal->getDono1()->getTelefone(),
            "dsEmail" => $Animal->getDono1()->getEmail(),
            "nrCRMV" => $Animal->getDono1()->getNrCRMV(),
            "selectCidadePessoa" => $selectCidadePessoa,
            "selectBairroPessoa" => $selectBairroPessoa,
            "selectLogradouroPessoa" => $selectLogradouroPessoa,
            "donoNaoDeclarado" => $Animal->getFlDonoNaoDeclarado() == 'S' ? true : false,

            "exibeExcluir" => $exibeExcluir,
            "exibeSalvar" => $exibeSalvar
        ]);

        $conteudoTela = $this->TelaComMenus->renderTelaComMenus($Cadastro);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => 'TelaMenus.css',
            'jsLinks' => 'cadastroAnimais.js',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
