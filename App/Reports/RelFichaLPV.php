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
        $dompdf->loadHtml('hello world');

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
