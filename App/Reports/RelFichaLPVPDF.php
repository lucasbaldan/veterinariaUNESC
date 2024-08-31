<?php

namespace App\Reports;


use Dompdf\Dompdf;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class RelFichaLPVPDF
{

    public static function gerar(Request $request, Response $response)
    {

        $conteudo = $request->getParsedBody();
        
        $dompdf = new Dompdf();
        $cdFichaLPV = !empty($conteudo['cdFichaLPV']) ? $conteudo['cdFichaLPV'] : '';

        if (!empty($cdFichaLPV)) {
            $html = new \App\Reports\ReportFont\ExameCitopalogico($cdFichaLPV);
            $html->generateReport();
            $html = $html->getHtml();
           
        } else {
            $html = 'Não foi possível gerar o pdf';
        }


        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

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
