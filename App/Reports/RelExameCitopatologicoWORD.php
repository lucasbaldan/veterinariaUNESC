<?php

namespace App\Reports;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class RelExameCitopatologicoWORD
{

    public static function gerar(Request $request, Response $response)
    {

        $conteudo = $request->getParsedBody();

        $cdFichaLPV = !empty($conteudo['cdFichaLPV']) ? $conteudo['cdFichaLPV'] : '';

        if (!empty($cdFichaLPV)) {
            $DadosFicha = \App\Models\Atendimentos::findById($cdFichaLPV);
        }


        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addImage('http://localhost/veterinaria/public/img/defaultCabecalhoFicha.png', [
            'width' => 600, 'height' => 100, 'align' => 'center'
        ]);
        
        // Adiciona o título centralizado
        $section->addTitle('EXAME CITOPATOLÓGICO', 2);
        
        // Adiciona a tabela
        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addText('Nome do Animal', ['bold' => true]);
        $table->addCell(4000)->addText($DadosFicha->getAnimal()->getNome());
        
        $table->addCell(2000)->addText('Espécie', ['bold' => true]);
        $table->addCell(4000)->addText($DadosFicha->getAnimal()->getEspecie()->getDescricao());
        
        $table->addCell(2000)->addText('Raça', ['bold' => true]);
        $table->addCell(4000)->addText($DadosFicha->getAnimal()->getRaca()->getDescricao());
        
        $table->addRow();
        $table->addCell(2000)->addText('Idade', ['bold' => true]);
        $table->addCell(4000)->addText($DadosFicha->getIdadeAno());
        
        $table->addCell(2000)->addText('Sexo', ['bold' => true]);
        $table->addCell(4000)->addText($DadosFicha->getAnimal()->getSexo());
        
        $table->addRow();
        $table->addCell(2000)->addText('Proprietário', ['bold' => true]);
        $table->addCell(8000)->addText($DadosFicha->getAnimal()->getDono1()->getNome());
        
        $table->addRow();
        $table->addCell(2000)->addText('Veterinário', ['bold' => true]);
        $table->addCell(8000)->addText($DadosFicha->getVeterinarioRemetente()->getNome());
        
        $section->addTextBreak(1);
        
        // Adiciona as demais informações
        $section->addText('Natureza do Material: ' . $DadosFicha->getMaterialRecebido(), ['bold' => true]);
        
        $section->addTextBreak(2);
        
        $section->addText('Descrição Microscópica:', ['bold' => true]);
        $section->addTextBreak(2);
        
        $section->addText('Diagnóstico/Conclusão: ' . $DadosFicha->getDiagnostico(), ['bold' => true]);
        
        $section->addTextBreak(2);
        
        $section->addText('Notas:', ['bold' => true]);
        
        $section->addTextBreak(2);
        
        $section->addText('Referências:', ['bold' => true]);
        
        $section->addTextBreak(3);
        
        $section->addText('Observação: este laudo, como todo resultado de análise laboratorial, deve ser submetido à avaliação do médico veterinário responsável, junto aos demais exames e histórico do paciente.', ['size' => 8]);
        
        $section->addTextBreak(1);
        
        // Adiciona a assinatura
        $section->addImage('http://localhost/veterinaria/public/img/AssClairton.png', [
            'width' => 150, 'height' => 50, 'align' => 'center'
        ]);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');

        // Capturando o conteúdo do documento na memória
        ob_start();
        $writer->save("php://output");
        $wordData = ob_get_contents();
        ob_end_clean();

        $response->getBody()->write($wordData);

        return $response->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->withHeader('Content-Disposition', 'attachment; filename="relatorio.docx"')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withHeader('Content-Length', (string)strlen($wordData))
            ->withStatus(200);
    }
}
