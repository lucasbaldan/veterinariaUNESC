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

        $header = $section->addHeader();

        // Adiciona a imagem ao cabeçalho
        $header->addImage(
            'https://lpvunesc.com.br/veterinaria/public/img/defaultCabecalhoFicha.png',
            [
                'width' => 450,
                'height' => 90,
                'align' => 'center'  // Centraliza a imagem no cabeçalho
            ]
        );
        // Adiciona o título centralizado
        $section->addText('EXAME CITOPATOLÓGICO', ['size' => 14, 'bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Adiciona a tabela
        $table = $section->addTable();
        $table->addRow(null, ['spaceAfter' => 0]);
        $table->addCell(5000)->addText('Nome do Animal:', ['bold' => true, 'size' => 10]);
        $table->addCell(4000)->addText($DadosFicha->getAnimal()->getNome());

        $table->addCell(3000)->addText('Espécie:', ['bold' => true, 'size' => 10]);
        $table->addCell(4000)->addText($DadosFicha->getAnimal()->getEspecie()->getDescricao());

        $table->addCell(2000)->addText('Raça:', ['bold' => true, 'size' => 10]);
        $table->addCell(4000)->addText($DadosFicha->getAnimal()->getRaca()->getDescricao());

        $table->addRow();
        $table->addCell(2000)->addText('Idade:', ['bold' => true, 'size' => 10]);
        $table->addCell(4000)->addText((empty($DadosFicha->getIdadeAno()) ? "" : $DadosFicha->getIdadeAno() . " Anos ") . (empty($DadosFicha->getIdadeMes()) ? "" : $DadosFicha->getIdadeMes() . " Meses ") . (empty($DadosFicha->getIdadeDia()) ? "" : $DadosFicha->getIdadeDia() . " Dias "));

        $table->addCell(2000)->addText('Sexo:', ['bold' => true, 'size' => 10]);
        $table->addCell(4000)->addText($DadosFicha->getAnimal()->getSexo() == 'M' ? "Macho" : "Fêmea");

        $table->addRow();
        $table->addCell(2000)->addText('Proprietário:', ['bold' => true, 'size' => 10]);
        $table->addCell(8500)->addText($DadosFicha->getAnimal()->getDono1()->getNome());

        $table->addRow();
        $table->addCell(2000)->addText('Veterinário:', ['bold' => true, 'size' => 10]);
        $table->addCell(8500)->addText($DadosFicha->getVeterinarioRemetente()->getNome());

        $section->addTextBreak(1);

        // Adiciona as demais informações
        $section->addText('Natureza do Material: ', ['bold' => true, 'size' => 12]);
        $section->addText($DadosFicha->getMaterialRecebido(), ['bold' => false, 'size' => 12]);

        $section->addTextBreak(1);

        $section->addText('Descrição Microscópica:', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(1);

        $section->addText('Diagnóstico/Conclusão: ', ['bold' => true, 'size' => 12]);
        $section->addText($DadosFicha->getDiagnostico(), ['bold' => false, 'size' => 12]);

        $section->addTextBreak(1);

        $section->addText('Notas:', ['bold' => true, 'size' => 12]);

        $section->addTextBreak(1);

        $section->addText('Referências:', ['bold' => true, 'size' => 12]);

        $section->addTextBreak(1);

        $section->addText('Observação: este laudo, como todo resultado de análise laboratorial, deve ser submetido à avaliação do médico veterinário responsável, junto aos demais exames e histórico do paciente.', ['size' => 10.5], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);

        // Adiciona a assinatura
        $section->addImage('https://lpvunesc.com.br/veterinaria/public/img/AssClairton.png', [
            'width' => 150,
            'height' => 70,
            'align' => 'center'
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
