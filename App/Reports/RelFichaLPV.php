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
<html lang="pt-br" style="background: #FFF;">
<head>
    <meta charset="utf-8">
    <title>Laboratório de Patologia Veterinária</title>
    <style>
        body.pdf-content {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .breadcrumb {
            padding: 10px 15px;
            background-color: #F8F9FA;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .breadcrumb-item a {
            text-decoration: none;
            color: #007BFF;
        }
        .breadcrumb-item.active {
            color: #6c757d;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 20px;
        }
        h3 {
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        p {
            margin: 5px 0;
        }
        
         .title {
            text-align: center;
            margin-bottom: 20px;
        }
        .subtitle {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2em;
            color: #555;
        }
    </style>
</head>
<body class="pdf-content">

<div class="title text-center">
    <h1>Laboratório de Patologia Veterinária</h1>
</div>
<div class="subtitle text-center">
    <h2>Ficha número ' . $ficha["CD_FICHA_LPV"] .'</h2>
</div>

<div class="container-fluid d-flex justify-content-center align-items-center mt-2">
    <div class="card rounded" style="min-width: 100%;">
        <div class="card-body border border-1 rounded-2">

            <h3>Detalhes do Animal Diagnosticado</h3>
            <p>Nome do Animal: ' . (!empty($ficha["NM_ANIMAL"]) ? $ficha["NM_ANIMAL"] : "-") . '</p>

            <h3>Veterinário Remetente</h3>
            <p>Nome do Veterinário: ' . (!empty($ficha["NM_VETERINARIO"]) ? $ficha["NM_VETERINARIO"] : "-") . '</p>

            <h3>Propriedade de Origem</h3>
            <p>Município de Origem: ' . (!empty($ficha["CD_CIDADE"]) ? $ficha["CD_CIDADE"] : "-") . '</p>

            <h3>Detalhes do Diagnóstico</h3>
            <p>Material Recebido: ' . (!empty($ficha["DS_MATERIAL_RECEBIDO"]) ? $ficha["DS_MATERIAL_RECEBIDO"] : "-") . '</p>
            <p>Diagnóstico Presuntivo: ' . (!empty($ficha["DS_DIAGNOSTICO_PRESUNTIVO"]) ? $ficha["DS_DIAGNOSTICO_PRESUNTIVO"] : "-") . '</p>
            <p>Avaliação Tumoral com Margem: ' . (!empty($ficha["FL_AVALIACAO_TUMORAL_COM_MARGEM"]) ? $ficha["FL_AVALIACAO_TUMORAL_COM_MARGEM"] : "-") . '</p>
            <p>Epidemiologia e História Clínica: ' . (!empty($ficha["DS_EPIDEMIOLOGIA_HISTORIA_CLINICA"]) ? $ficha["DS_EPIDEMIOLOGIA_HISTORIA_CLINICA"] : "-") . '</p>
            <p>Lesões Macroscópicas: ' . (!empty($ficha["DS_LESOES_MACROSCOPICAS"]) ? $ficha["DS_LESOES_MACROSCOPICAS"] : "-") . '</p>
            <p>Lesões Histológicas: ' . (!empty($ficha["DS_LESOES_HISTOLOGICAS"]) ? $ficha["DS_LESOES_HISTOLOGICAS"] : "-") . '</p>
            <p>Diagnóstico: ' . (!empty($ficha["DS_DIAGNOSTICO"]) ? $ficha["DS_DIAGNOSTICO"] : "-") . '</p>
            <p>Relatório: ' . (!empty($ficha["DS_RELATORIO"]) ? $ficha["DS_RELATORIO"] : "-") . '</p>

        </div>
    </div>
</div>

</body>
</html>';


            // $html = '
            //         <!DOCTYPE html>
            //         <html lang="pt-br" style="background: #FFF">
            //         <head>
            //             <meta charset="utf-8">
            //             <title>Ficha de Diagnóstico Patologia</title>
            //             <!-- Incluir seus estilos e scripts necessários aqui -->
            //         </head>
            //         <body class="pdf-content">

            //         <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'8\' height=\'8\'%3E%3Cpath d=\'M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z\' fill=\'%236c757d\'/%3E%3C/svg%3E&#34;); background-color: #F8F9FA ;"
            //             aria-label="breadcrumb">
            //             <ol class="breadcrumb">
            //                 <li class="breadcrumb-item"><a href="/veterinariaUNESC/paginas/inicial"><i
            //                             class="bi bi-house-door-fill"></i></a></li>
            //                 <li class="breadcrumb-item" aria-current="page">Ficha de Diagnóstico Patologia</li>
            //                 <li class="breadcrumb-item" aria-current="page">Cadastro</li>
            //             </ol>
            //         </nav>

            //         <div class="container-fluid d-flex justify-content-center align-items-center mt-2">
            //             <div class="card rounded" style="min-width: 100%;">
            //                 <div class="card-body border border-1S rounded-2">

            //                     <h3>Detalhes do Animal Diagnosticado</h3>
            //                     <p>Nome do Animal: ' . $ficha['NM_ANIMAL'] . '</p>
            //                     <p>Tipo de Animal: ' . $ficha['NM_TIPO_ANIMAL'] . '</p>
            //                     <!-- <p>Espécie: {{especieAnimal}}</p> -->
            //                     <!-- <p>Raça: {{racaAnimal}}</p> -->
            //                     <!-- Adicione mais campos conforme necessário -->

            //                     <!-- <h3>Detalhes do Proprietário do Animal</h3> -->
            //                     <!-- <p>Nome do Proprietário: {{nmProprietario}}</p> -->
            //                     <!-- <p>Telefone: {{nrTelefoneProprietario}}</p> -->
            //                     <!-- Adicione mais campos conforme necessário -->

            //                     <h3>Veterinário Remetente</h3>
            //                     <p>Nome do Veterinário: '. $ficha['NM_VETERINARIO'] . '</p>
            //                     <!-- <p>CRMV: {{crmvVeterinarioRemetente}}</p>
            //                     <!-- <p>Telefone: {{nrTelVeterinarioRemetente}}</p> -->
            //                     <!-- <p>Email: {{dsEmailVeterinarioRemetente}}</p> -->
            //                     <!-- Adicione mais campos conforme necessário -->

            //                     <h3>Propriedade de Origem</h3>
            //                     <p>Município de Origem: '. $ficha['CD_CIDADE'] . '</p>
            //                     <!-- <p>Total de Animais na Propriedade: {{totalAnimais}}</p> -->
            //                     <!-- <p>Animais Doentes: {{qtdAnimaisDoentes}}</p> -->
            //                     <!-- <p>Animais Mortos: {{qtdAnimaisMortos}}</p> -->
            //                     <!-- Adicione mais campos conforme necessário -->

            //                     <h3>Detalhes do Diagnóstico</h3>
            //                     <p>Material Recebido: '. $ficha['DS_MATERIAL_RECEBIDO'] . '</p>
            //                     <p>Diagnóstico Presuntivo: '. $ficha['DS_DIAGNOSTICO_PRESUNTIVO'] . '</p>
            //                     <p>Avaliação Tumoral com Margem: '. $ficha['FL_AVALIACAO_TUMORAL_COM_MARGEM'] . '</p>
            //                     <p>Epidemiologia e História Clínica: '. $ficha['DS_EPIDEMIOLOGIA_HISTORIA_CLINICA'] . '</p>
            //                     <p>Lesões Macroscópicas: '. $ficha['DS_LESOES_MACROSCOPICAS'] . '</p>
            //                     <p>Lesões Histológicas: '. $ficha['DS_LESOES_HISTOLOGICAS'] .'</p>
            //                     <p>Diagnóstico: '. $ficha['DS_DIAGNOSTICO'] . '</p>
            //                     <p>Relatório: '. $ficha['DS_RELATORIO'] . '</p>
            //                     <!-- Adicione mais campos conforme necessário -->

            //                 </div>
            //             </div>
            //         </div>

            //         </body>
            //         </html>';

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
