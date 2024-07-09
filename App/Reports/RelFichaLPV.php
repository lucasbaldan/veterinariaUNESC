<?php

namespace App\Reports;

use Dompdf\Dompdf;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class RelFichaLPV
{

    public static function gerar(Request $request, Response $response)
    {

        $dompdf = new Dompdf();
        $conteudo = $request->getParsedBody();

        $cdFichaLPV = !empty($conteudo['cdFichaLPV']) ? $conteudo['cdFichaLPV'] : '';

        if (!empty($cdFichaLPV)) {
            $ficha = \App\Models\FormularioLPV::RetornarDadosRelatorioFichaLPV($cdFichaLPV);

            $html = '
                    <!DOCTYPE html>
                    <html lang="pt-br" style="background: #FFF">
                    <head>
                        <meta charset="utf-8">
                        <title>Ficha de Diagnóstico Patologia</title>
                        <!-- Incluir seus estilos e scripts necessários aqui -->
                    </head>
                    <body class="pdf-content">

                    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'8\' height=\'8\'%3E%3Cpath d=\'M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z\' fill=\'%236c757d\'/%3E%3C/svg%3E&#34;); background-color: #F8F9FA ;"
                        aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/veterinariaUNESC/paginas/inicial"><i
                                        class="bi bi-house-door-fill"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Ficha de Diagnóstico Patologia</li>
                            <li class="breadcrumb-item" aria-current="page">Cadastro</li>
                        </ol>
                    </nav>

                    <div class="container-fluid d-flex justify-content-center align-items-center mt-2">
                        <div class="card rounded" style="min-width: 100%;">
                            <div class="card-body border border-1S rounded-2">

                                <h3>Detalhes do Animal Diagnosticado</h3>
                                <p>Nome do Animal: ' . $ficha['NM_ANIMAL'] . '</p>
                                <p>Tipo de Animal: ' . $ficha['NM_TIPO_ANIMAL'] . '</p>
                                <!-- <p>Espécie: {{especieAnimal}}</p> -->
                                <!-- <p>Raça: {{racaAnimal}}</p> -->
                                <!-- Adicione mais campos conforme necessário -->

                                <!-- <h3>Detalhes do Proprietário do Animal</h3> -->
                                <!-- <p>Nome do Proprietário: {{nmProprietario}}</p> -->
                                <!-- <p>Telefone: {{nrTelefoneProprietario}}</p> -->
                                <!-- Adicione mais campos conforme necessário -->

                                <h3>Veterinário Remetente</h3>
                                <p>Nome do Veterinário: '. $ficha['NM_VETERINARIO'] . '</p>
                                <!-- <p>CRMV: {{crmvVeterinarioRemetente}}</p>
                                <!-- <p>Telefone: {{nrTelVeterinarioRemetente}}</p> -->
                                <!-- <p>Email: {{dsEmailVeterinarioRemetente}}</p> -->
                                <!-- Adicione mais campos conforme necessário -->

                                <h3>Propriedade de Origem</h3>
                                <p>Município de Origem: '. $ficha['CD_CIDADE'] . '</p>
                                <!-- <p>Total de Animais na Propriedade: {{totalAnimais}}</p> -->
                                <!-- <p>Animais Doentes: {{qtdAnimaisDoentes}}</p> -->
                                <!-- <p>Animais Mortos: {{qtdAnimaisMortos}}</p> -->
                                <!-- Adicione mais campos conforme necessário -->

                                <h3>Detalhes do Diagnóstico</h3>
                                <p>Material Recebido: '. $ficha['DS_MATERIAL_RECEBIDO'] . '</p>
                                <p>Diagnóstico Presuntivo: '. $ficha['DS_DIAGNOSTICO_PRESUNTIVO'] . '</p>
                                <p>Avaliação Tumoral com Margem: '. $ficha['FL_AVALIACAO_TUMORAL_COM_MARGEM'] . '</p>
                                <p>Epidemiologia e História Clínica: '. $ficha['DS_EPIDEMIOLOGIA_HISTORIA_CLINICA'] . '</p>
                                <p>Lesões Macroscópicas: '. $ficha['DS_LESOES_MACROSCOPICAS'] . '</p>
                                <p>Lesões Histológicas: {{dsLesoesHistologicas}}</p>
                                <p>Diagnóstico: '. $ficha['DS_DIAGNOSTICO'] . '</p>
                                <p>Relatório: '. $ficha['DS_RELATORIO'] . '</p>
                                <!-- Adicione mais campos conforme necessário -->

                            </div>
                        </div>
                    </div>

                    </body>
                    </html>';

        } else {
            $html = 'Não foi possível gerar o pdf';
        }

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $pdfOutput = $dompdf->output();

        $response->getBody()->write($pdfOutput);

        return $response->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="relatorio.pdf"')
            ->withStatus(200);
    }
}
