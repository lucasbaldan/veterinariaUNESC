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

        $exibeSalvar = true;
        $exbieExcluir = true;

        $idAlteracao = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';

        $Animal = \App\Models\Animais::findById($idAlteracao);

        if(!empty($Animal->getCodigo())){
            $selectTipoAnimal = '<option value="'.$Animal->getTipoAnimal()->getCodigo().'">'.$Animal->getTipoAnimal()->getDescricao().'</option>';
            $selectEspecie = '<option value="'.$Animal->getEspecie()->getCodigo().'">'.$Animal->getEspecie()->getDescricao().'</option>';
            $selectRaca = '<option value="'.$Animal->getRaca()->getCodigo().'">'.$Animal->getRaca()->getDescricao().'</option>';

            if(!empty($Animal->getDono1()->getCodigo())){
                $selectCidadePessoa = '<option value="'.$Animal->getDono1()->getCidade()->getCodigo().'">'.$Animal->getDono1()->getCidade()->getDescricao().'</option>';
                $selectBairroPessoa = '<option value="'.$Animal->getDono1()->getBairro()->getCodigo().'">'.$Animal->getDono1()->getBairro()->getNome().'</option>';
                $selectLogradouroPessoa = '<option value="'.$Animal->getDono1()->getLogradouro()->getCodigo().'">'.$Animal->getDono1()->getLogradouro()->getNome().'</option>';
            }
            else {
                $selectBairroPessoa = '';
                $selectCidadePessoa = '';
                $selectLogradouroPessoa = '';
            }
            
        } else {
            $exbieExcluir = false;
            $selectTipoAnimal = '';
            $selectEspecie = '';
            $selectRaca = '';
            $selectBairroPessoa = '';
            $selectCidadePessoa = '';
            $selectLogradouroPessoa = '';
        }

        $selectSexoAnimal = '<select class="form-select" id="dsSexo" name="dsSexo" aria-label="Sexo Animal">
                                    <option value="">Selecione...</option>
                                    <option value="M" '.($Animal->getSexo() == 'M' ? 'selected' : ' ').'>Macho</option>
                                    <option value="F" '.($Animal->getSexo() == 'F' ? 'selected' : ' ').'>Fêmea</option>
                                </select>';


        

        $Cadastro = $this->twig->fetch('cadastroAnimais.twig', [
            "cdAnimal" => $Animal->getCodigo(),
            "animal" => $Animal->getNome(),
            "selectTipoAnimal" => $selectTipoAnimal,
            "selectEspecieAnimal" => $selectEspecie,
            "selectRacaAnimal" => $selectRaca,
            "selectSexoAnimal" => $selectSexoAnimal,
            "idadeAnimal" => $Animal->getIdadeAproximada(),
            "anoNascimentoAnimal" => $Animal->getAnoNascimento(),

            "cdPessoa" => $Animal->getDono1()->getCodigo(),
            "nmPessoa" => $Animal->getDono1()->getNome(),
            "cpfPessoa" => $Animal->getDono1()-> getCPF(),
            "dataNascimento" => $Animal->getDono1()->getDataNascimento(),
            "nrTelefone" => $Animal->getDono1()->getTelefone(),            
            "dsEmail" => $Animal->getDono1()->getEmail(),
            "nrCRMV" => $Animal->getDono1()->getNrCRMV(),
            "selectCidadePessoa" => $selectCidadePessoa,
            "selectBairroPessoa" => $selectBairroPessoa,
            "selectLogradouroPessoa" => $selectLogradouroPessoa,

            "exibeExcluir" => $exbieExcluir,
            "exibeSalvar" => $exibeSalvar
        ]);

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $Cadastro]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'cssLinks' => 'TelaMenus.css',
            'jsLinks' => 'cadastroAnimais.js',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
