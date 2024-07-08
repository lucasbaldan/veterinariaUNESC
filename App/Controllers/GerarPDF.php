<?php

namespace App\Controllers;

use Knp\Snappy\Pdf;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GerarPDF
{
    public static function GerarPdf(Request $request, Response $response)
    {
        // Caminho ajustável para o binário wkhtmltopdf
        $snappyPath = 'C:\wkhtmltopdf\bin\wkhtmltopdf'; // Ajuste para o caminho correto do seu sistema
        $snappy = new Pdf($snappyPath);
    
        $conteudo = $request->getParsedBody();
    
        $conteudoHTML = !empty($conteudo['html']) ? $conteudo['html'] : '';
        $nmArquivo = !empty($conteudo['nmArquivo']) ? $conteudo['nmArquivo'] : 'PDF_' . date('m-d-Y');
        $orientacao = !empty($conteudo['orientacao']) ? $conteudo['orientacao'] : 'Portrait';

        // echo $conteudoHTML;
    
        // Monta o HTML final
        // $html = <<<EOD
        // <h1>$nmArquivo</h1>
        // <h2>TESTEEE</h2>
        // <br/>
        // $html
        // EOD;

        $html = '   <!DOCTYPE html>
                    <html lang="pt-br" style="background: #FFF">
                        <head>
                            <meta charset="utf-8">
                            <link href="https://smartirriga.com/Painel/Layout/css/bootstrap.min.css" rel="stylesheet">';

        $html .= '      </head>
                        <body class="pdf-content">
                            ' . $conteudoHTML .
            '           </body>
                    </html>';
    
        // Configurações do PDF
        $options = [
            'orientation' => strtolower($orientacao), // Certifica-se de que a orientação está em minúsculas
            'title' => $nmArquivo,
            'encoding' => 'UTF-8'
        ];
    
        try {
            // Gera o conteúdo do PDF
            // $pdfContent = $snappy->getOutputFromHtml($html, $options);
    
            // Define os headers da resposta
            $response = $response->withHeader('Content-Type', 'application/pdf');
            echo $snappy->getOutput('http://www.schoolofnet.com/blog/');
                                //  ->withHeader('Content-Disposition', 'attachment; filename="'.$nmArquivo.'.pdf"');
    
            // Escreve o conteúdo do PDF na resposta
            // $response->getBody()->write($pdfContent);
    
            return $response;
        } catch (\Exception $e) {
            // Trata erros e retorna uma mensagem de erro adequada
            $response->getBody()->write('Erro ao gerar PDF: ' . $e->getMessage());
            return $response->withStatus(500);
        }
    }
 

    // public static function GerarPdf(Request $request, Response $response)
    // {
    //     // $snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
    //     $snappy = new Pdf('C:\wkhtmltopdf\bin\wkhtmltopdf');
    
    //     $conteudo = $request->getParsedBody();
    
    //     $html = !empty($conteudo['html']) ? $conteudo['html'] : '';
    //     $nmArquivo = !empty($conteudo['nmArquivo']) ? $conteudo['nmArquivo'] : 'PDF_' . date('m-d-Y');
    //     $orientacao = !empty($conteudo['orientacao']) ? $conteudo['orientacao'] : 'Portrait';
    
    //     // Monta o HTML final
    //     $html = <<<EOD
    //     <h1>$nmArquivo</h1>
    //     <br/>
    //     $html
    //     EOD;
    
    //     // Configurações do PDF
    //     $options = [
    //         'orientation' => strtolower($orientacao), // Certifica-se de que a orientação está em minúsculas
    //         'title' => $nmArquivo,
    //         'encoding' => 'UTF-8'
    //     ];
    
    //     try {
    //         // Gera o conteúdo do PDF
    //         $pdfContent = $snappy->getOutputFromHtml($html, array('orientation' => strtolower($orientacao)));

    //         header('Content-Type: application/pdf');
    //         header('Content-Disposition: inline; filename="'. $nmArquivo .'.pdf"');
    //         echo $pdfContent;
    
    //         // Define os headers da resposta
    //         // $response = $response->withHeader('Content-Type', 'application/pdf')->withHeader('Content-Disposition', 'attachment; filename="'.$nmArquivo.'.pdf"');
    
    //         // Escreve o conteúdo do PDF na resposta
    //         // $response->getBody()->write($pdfContent);

    //         // return $response;
    //     } catch (\Exception $e) {
    //         // Trata erros e retorna uma mensagem de erro adequada
    //         $response->getBody()->write('Erro ao gerar PDF: ' . $e->getMessage());
    //         return $response->withStatus(500);
    //     }
    // }
}
