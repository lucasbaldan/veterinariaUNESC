<?php

namespace App\Controllers;

use Knp\Snappy\Pdf;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GerarPDF
{
    protected $snappy;

    public function GerarPdf(Request $request, Response $response)
    {
        // Caminho ajustável para o binário wkhtmltopdf
        $snappyPath = 'C:\wkhtmltopdf\bin\wkhtmltopdf'; // Ajuste para o caminho correto do seu sistema
        $this->snappy = new Pdf($snappyPath);

        // $this->snappy = new Pdf();
        // $this->snappy->setBinary('/usr/local/bin/wkhtmltopdf');

        $conteudo = $request->getParsedBody();

        $conteudoHTML = !empty($conteudo['html']) ? $conteudo['html'] : '';
        $nmArquivo = !empty($conteudo['nmArquivo']) ? $conteudo['nmArquivo'] : 'PDF_' . date('m-d-Y');
        $orientacao = !empty($conteudo['orientacao']) ? $conteudo['orientacao'] : 'Portrait';

        // var_dump($conteudoHTML);


        $html = '   <!DOCTYPE html>
                    <html lang="pt-br" style="background: #FFF">
                        <head>
                            <meta charset="utf-8">

                        </head>
                        <body class="pdf-content">
                            ' . $conteudoHTML .
            '           </body>
                    </html>';

        try {
            $response = $this->snappy->getOutputFromHtml($html, array('orientation' => $orientacao));
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $nmArquivo . '".pdf');
            echo $response;
        } catch (\Exception $e) {
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



// public function GerarPdf(Request $request, Response $response)
// {
//     // Caminho ajustável para o binário wkhtmltopdf
//     // $snappyPath = 'C:\wkhtmltopdf\bin\wkhtmltopdf'; // Ajuste para o caminho correto do seu sistema
//     // $this->snappy = new Pdf($snappyPath);

//     $this->snappy = new Pdf();
//     $this->snappy->setBinary('/usr/local/bin/wkhtmltopdf');

//     $conteudo = $request->getParsedBody();

//     $conteudoHTML = !empty($conteudo['html']) ? $conteudo['html'] : '';
//     $nmArquivo = !empty($conteudo['nmArquivo']) ? $conteudo['nmArquivo'] : 'PDF_' . date('m-d-Y');
//     $orientacao = !empty($conteudo['orientacao']) ? $conteudo['orientacao'] : 'Portrait';

//     $html = '<!DOCTYPE html>
//             <html lang="pt-br" style="background: #FFF">
//                 <head>
//                     <meta charset="utf-8">
//                 </head>
//                 <body class="pdf-content">' 
//                 . $conteudoHTML . 
//                 '</body>
//             </html>';

//     // Inicie o buffer de saída no início do script
//     ob_start();

//     try {
//         $pdfContent = $this->snappy->getOutputFromHtml($html, ['orientation' => $orientacao]);
        
//         // Limpe (descarta) o conteúdo do buffer de saída e o desliga
//         ob_end_clean();

//         // Defina os cabeçalhos antes de enviar qualquer conteúdo
//         header('Content-Type: application/pdf');
//         header('Content-Disposition: inline; filename="' . $nmArquivo . '.pdf"');
//         header('Content-Length: ' . strlen($pdfContent));

//         // Envie o conteúdo PDF
//         echo $pdfContent;
//     } catch (\Exception $e) {
//         // Limpe o buffer de saída em caso de exceção
//         ob_end_clean();
        
//         // Defina uma resposta de erro
//         $response->getBody()->write('Erro ao gerar PDF: ' . $e->getMessage());
//         return $response->withStatus(500);
//     }

//     // Retorne a resposta com o conteúdo do PDF
//     return $response->withHeader('Content-Type', 'application/pdf')
//                     ->withHeader('Content-Disposition', 'inline; filename="' . $nmArquivo . '.pdf"')
//                     ->withBody(new \Slim\Http\Stream(fopen('php://temp', 'r+')));
// }