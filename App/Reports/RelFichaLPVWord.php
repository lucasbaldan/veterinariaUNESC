<?php

namespace App\Reports;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class RelFichaLPVWord
{

    public static function gerar(Request $request, Response $response)
    {

        $conteudo = $request->getParsedBody();

        $cdFichaLPV = !empty($conteudo['cdFichaLPV']) ? $conteudo['cdFichaLPV'] : '';

        if (!empty($cdFichaLPV)) {
            $html = new \App\Reports\ReportFont\ExameCitopalogico($cdFichaLPV);
            $html->generateReport();
            $html = $html->getHtml();
           
        } else {
            $html = 'Não foi possível gerar o pdf';
        }


        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        Html::addHtml($section, $html, false, false);

        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        $fileContent = file_get_contents($tempFile);
        unlink($tempFile);

        $response->getBody()->write($fileContent);

        return $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->withHeader('Content-Disposition', 'attachment; filename="relatorio.docx"')
            ->withStatus(200);
    }
}
