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
        $exibeSalvarGaleria = false;

        if (!empty($idAnimal)) {
            $exibeExcluir = false;
            $exibeSalvarGaleria = true;

            $AnimalFicha = \App\Models\Animais::findById($idAnimal);

            $selectTipoAnimal = '<option value="' . $AnimalFicha->getTipoAnimal()->getCodigo() . '" selected>' . $AnimalFicha->getTipoAnimal()->getDescricao() . '</option>';
            $selectEspecie = '<option value="' . $AnimalFicha->getEspecie()->getCodigo() . '" selected>' . $AnimalFicha->getEspecie()->getDescricao() . '</option>';
            $selectRaca = '<option value="' . $AnimalFicha->getRaca()->getCodigo() . '" selected>' . $AnimalFicha->getRaca()->getDescricao() . '</option>';

            $selectSexoAnimal = '<select class="form-select" id="dsSexo" name="dsSexo" aria-label="Sexo Animal" disabled>
                                    <option value="">Selecione...</option>
                                    <option value="M" ' . ($AnimalFicha->getSexo() == 'M' ? 'selected' : ' ') . '>Macho</option>
                                    <option value="F" ' . ($AnimalFicha->getSexo() == 'F' ? 'selected' : ' ') . '>Fêmea</option>
                                </select>';

            $selectTumoralMargem = '<select class="form-select" id="flAvaliacaoTumoralComMargem" name="flAvaliacaoTumoralComMargem">
                        <option value="N">Não</option>
                        <option value="S">Sim</option>
                    </select>';

            $formulario = $this->twig->fetch('formularioLPV.twig', [
                "DataFicha" => date('Y-m-d'),
                "cdAnimal" => $AnimalFicha->getCodigo(),
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

                "selectTurmoralcomMargem" => $selectTumoralMargem,

                "exibeGaleria" => false,
                "exibeExcluir" => $exibeExcluir,
                "exibeSalvarGaleria" => $exibeSalvarGaleria,
                "exibeSalvar" => $exibeSalvar,
                "nomeSalvar" => "Salvar e Sair"
            ]);
        } elseif (!empty($idFichaAlteracao)) {
            $Ficha = \App\Models\Atendimentos::findById($idFichaAlteracao);
            $AnimalFicha = $Ficha->getAnimal();
            $urlGaleria = $Ficha->getImagesIds();

            $selectTipoAnimal = '<option value="' . $AnimalFicha->getTipoAnimal()->getCodigo() . '" selected>' . $AnimalFicha->getTipoAnimal()->getDescricao() . '</option>';
            $selectEspecie = '<option value="' . $AnimalFicha->getEspecie()->getCodigo() . '" selected>' . $AnimalFicha->getEspecie()->getDescricao() . '</option>';
            $selectRaca = '<option value="' . $AnimalFicha->getRaca()->getCodigo() . '" selected>' . $AnimalFicha->getRaca()->getDescricao() . '</option>';

            $selectSexoAnimal = '<select class="form-select" id="dsSexo" name="dsSexo" aria-label="Sexo Animal" disabled>
                                    <option value="">Selecione...</option>
                                    <option value="M" ' . ($AnimalFicha->getSexo() == 'M' ? 'selected' : ' ') . '>Macho</option>
                                    <option value="F" ' . ($AnimalFicha->getSexo() == 'F' ? 'selected' : ' ') . '>Fêmea</option>
                                </select>';

            $selectCidadeVeterinario = '<option value="' . $Ficha->getVeterinarioRemetente()->getCidade()->getCodigo() . '" selected>' . $Ficha->getVeterinarioRemetente()->getCidade()->getDescricao() . '</option>';

            $selectMunicipioOrigem = '<option value="' . $Ficha->getCidadeOrigem()->getCodigo() . '" selected>' . $Ficha->getCidadeOrigem()->getDescricao() . '</option>';

            $selectTumoralMargem = '<select class="form-select" id="flAvaliacaoTumoralComMargem" name="flAvaliacaoTumoralComMargem">
                        <option value="N" ' . ($Ficha->getAvalicaoTumoralMargem() == 'N' ? 'selected' : '') . '>Não</option>
                        <option value="S" ' . ($Ficha->getAvalicaoTumoralMargem() == 'S' ? 'selected' : '') . '>Sim</option>
                    </select>';

            $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('FICHA_LPV', 'FL_EDITAR');
            $exibeSalvar = $permissaoSalvar == true ? true : false;

            $permissaoExcluir = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('FICHA_LPV', 'FL_EXCLUIR');
            $exibeExcluir = $permissaoExcluir == true ? true : false;


            $formulario = $this->twig->fetch('formularioLPV.twig', [
                "cdFichaLPV" => $Ficha->getCodigo(),
                "DataFicha" => $Ficha->getData(),
                "cdAnimal" => $AnimalFicha->getCodigo(),
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

                "cdVeterinarioRemetente" => $Ficha->getVeterinarioRemetente()->getCodigo(),
                "nmVeterinarioRemetente" => $Ficha->getVeterinarioRemetente()->getNome(),
                "crmvVeterinario" => $Ficha->getVeterinarioRemetente()->getNrCRMV(),
                "telefoneVeterinario" => $Ficha->getVeterinarioRemetente()->getTelefone(),
                "emailVeterinario" => $Ficha->getVeterinarioRemetente()->getEmail(),
                "selectCidadeVeterinario" =>  $selectCidadeVeterinario,

                "selectMunicipioFicha" => $selectMunicipioOrigem,
                "totalAnimais" => $Ficha->getTotalAnimais(),
                "animaisDoentes" => $Ficha->getAnimaisDoentes(),
                "animaisMortos" => $Ficha->getAnimaisMortos(),
                "materialRecebido" => $Ficha->getMaterialRecebido(),
                "DiagnosticoPresuntivo" => $Ficha->getDiagnostico(),
                "selectTurmoralcomMargem" => $selectTumoralMargem,
                "epidemiologia" => $Ficha->getEpidemiologia(),
                "LesoesMacroscopicas" => $Ficha->getLessoesMacroscopias(),
                "LesoesHistologicas" => $Ficha->getLessoesHistologicas(),
                "diagnostico" => $Ficha->getDiagnostico(),
                "relatorio" => $Ficha->getRelatorio(),
                "urlGaleria" => $urlGaleria,

                "exibeGaleria" => true,
                "exibeExcluir" => $exibeExcluir,
                "exibeSalvarGaleria" => false,
                "exibeSalvar" => $exibeSalvar,
                "nomeSalvar" => "Salvar" 
            ]);
        } else {
            $formulario = '<div class="alert alert-danger" role="alert">
                                    <i class="bi bi-patch-exclamation"></i>  Houve uma falha ao tentar processar a ação desejada, tente novamente mais tarde.
                                    <br>
                                    Caso o problema persista, contate o administrador do sistema!
                                </div>';
        }

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $formulario]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => 'TelaMenus.css;formularioLPV.css',
            'jsLinks' => 'formularioLPV.js',
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
