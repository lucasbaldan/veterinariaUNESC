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
        $post = $request->getParsedBody();
        $idAnimal = isset($post['idAnimal']) ? $post['idAnimal'] : '';
        $idFichaAlteracao = isset($post['idFicha']) ? $post['idFicha'] : '';

        $exibeSalvar = true;
        $exibeExcluir = true;

        if (!empty($idAnimal)) {
            $exibeExcluir = false;

            $AnimalFicha = \App\Models\Animais::findById($idAnimal);

                $selectTipoAnimal = '<option value="' . $AnimalFicha->getTipoAnimal()->getCodigo() . '" selected>' . $AnimalFicha->getTipoAnimal()->getDescricao() . '</option>';
                $selectEspecie = '<option value="' . $AnimalFicha->getEspecie()->getCodigo() . '" selected>' . $AnimalFicha->getEspecie()->getDescricao() . '</option>';
                $selectRaca = '<option value="' . $AnimalFicha->getRaca()->getCodigo() . '" selected>' . $AnimalFicha->getRaca()->getDescricao() . '</option>';

                $selectSexoAnimal = '<select class="form-select" id="dsSexo" name="dsSexo" aria-label="Sexo Animal" disabled>
                                    <option value="">Selecione...</option>
                                    <option value="M" '.($AnimalFicha->getSexo() == 'M' ? 'selected' : ' ').'>Macho</option>
                                    <option value="F" '.($AnimalFicha->getSexo() == 'F' ? 'selected' : ' ').'>Fêmea</option>
                                </select>';

                $formulario = $this->twig->fetch('formularioLPV.twig', [
                    "DataFicha" => date('Y-m-d'),
                    "animal" => $AnimalFicha->getNome(),
                    "selectTipoAnimal" => $selectTipoAnimal,
                    "selectEspecieAnimal" => $selectEspecie,
                    "selectRacaAnimal" => $selectRaca,
                    "selectSexoAnimal" => $selectSexoAnimal,
                    "idadeAnimal" => $AnimalFicha->getIdadeAproximada(),
                    "anoNascimentoAnimal" => $AnimalFicha->getAnoNascimento(),

                    "nmDonoAnimal" => $AnimalFicha->getDono1()->getNome(),
                    "nrTelefoneDono" => $AnimalFicha->getDono1()->getTelefone(),
                    "donoNaoDeclarado" => $AnimalFicha->getFlDonoNaoDeclarado() == 'S' ? true : false,

                    "exibeExcluir" => $exibeExcluir,
                    "exibeSalvar" => $exibeSalvar
                ]);
            
        } elseif (!empty($idFichaAlteracao)) {
            $Ficha = \App\Models\Atendimentos::findById($idFichaAlteracao);
        } else {
                $formulario = '<div class="alert alert-danger" role="alert">
                                    <i class="bi bi-patch-exclamation"></i>  Houve uma falha ao tentar processar a ação desejada, tente novamente mais tarde.
                                    <br>
                                    Caso o problema persista, contate o administrador do sistema!
                                </div>';
        }

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $formulario]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'cssLinks' => 'TelaMenus.css;formularioLPV.css',
            'jsLinks' => 'formularioLPV.js',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
