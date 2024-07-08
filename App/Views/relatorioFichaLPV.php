<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class RelatorioFichaLPV
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $exibeFichas = false;
        $table = '';

        $Formulario = $request->getParsedBody();
        $filtrar = !empty($Formulario['filtrar']) ? $Formulario['filtrar'] : '';
        $cdAnimal = !empty($Formulario['cdAnimal']) ? $Formulario['cdAnimal'] : '';
        $cdCidade = !empty($Formulario['cdCidade']) ? $Formulario['cdCidade'] : '';
        $cdVetRemetente = !empty($Formulario['cdVetRemetente']) ? $Formulario['cdVetRemetente'] : '';
        $dtInicialFicha = !empty($Formulario['dtInicialFicha']) ? $Formulario['dtInicialFicha'] : '';
        $dtFinalFicha = !empty($Formulario['dtFinalFicha']) ? $Formulario['dtFinalFicha'] : '';
        $flAvaliacaoTumoral = !empty($Formulario['flAvaliacaoTumoral']) ? $Formulario['flAvaliacaoTumoral'] : '';


        $veterinarioRemetente = \App\Models\Pessoas::findById($cdVetRemetente);
        $cidade = \App\Models\Municipios::findById($cdCidade);
        $animal = \App\Models\TipoAnimais::findById($cdAnimal);
        
        $selectAnimal = '<option value="' . ($animal->getCodigo()) . '">' . ($animal->getDescricao()) . '</option>';
        $selectCidade = '<option value="' . ($cidade->getCodigo()) . '">' . ($cidade->getDescricao()) . '</option>';
        $selectVeterinarioRemetente = '<option value="' . ($veterinarioRemetente->getCodigo()) . '">' . ($veterinarioRemetente->getNome()) . '</option>';

        if (!empty($filtrar)) {
            $retorno = \App\Models\FormularioLPV::RetornaFichasFiltradas($cdAnimal, $cdCidade, $cdVetRemetente, $dtInicialFicha, $dtFinalFicha, $flAvaliacaoTumoral);
        }

        if (!empty($filtrar)) {
            if (!empty($retorno)) {

                $exibeFichas = true;
                $table = "<table class='table'>
                                <thead>
                                    <tr>
                                        <th>Veterinário Remetente</th>
                                        <th>Data</th>
                                        <th>Cidade Propriedades</th>
                                        <th>Animal</th>
                                    </tr>
                                </thead>
                                <tbody> 
                ";
                foreach ($retorno as $ficha) {
                    $dataFormatada = \App\Helpers\UteisAleatorios::FormataDataDoBanco($ficha['DT_FICHA']);
                    $table .= "<tr>
                                <td>" . $ficha['NM_VETERINARIO_REMETENTE'] . "</td>
                                <td>" . $dataFormatada . "</td>
                                <td>" . $ficha['NM_CIDADE'] . "</td>
                                <td>" . $ficha['NM_TIPO_ANIMAL'] . "</td>
                                </tr>";
                }

                $table .= "</tbody>
                            </table>";
            } else {
                $exibeFichas = true;
                $table = "<h6 class='text-center'>Nenhum dado retornado</h6>";
            }
        }

        $telaRelatorioFichas = $this->twig->fetch('relatorioFichaLPV.twig', [
            "dtInicialFicha" => $dtInicialFicha,
            "dtFinalFicha" => $dtFinalFicha,
            "selectAnimal" => $selectAnimal,
            "selectCidade" => $selectCidade,
            "selectVeterinarioRemetente" => $selectVeterinarioRemetente,
            "exibeFichas" => $exibeFichas,
            "table" => $table
        ]);

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $telaRelatorioFichas]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "relatorioFichaLPV.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
