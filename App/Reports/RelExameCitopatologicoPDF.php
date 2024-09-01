<?php

namespace App\Reports;


use Dompdf\Dompdf;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class RelExameCitopatologicoPDF
{

    public static function gerar(Request $request, Response $response)
    {

        $conteudo = $request->getParsedBody();

        $dompdf = new Dompdf(array('enable_remote' => true));
        $cdFichaLPV = !empty($conteudo['cdFichaLPV']) ? $conteudo['cdFichaLPV'] : '';

        if (!empty($cdFichaLPV)) {
            $DadosFicha = \App\Models\Atendimentos::findById($cdFichaLPV);

            if (!$DadosFicha) $html = 'Erro interno ao gerar o documento';


            $html = '   <html lang="pt-BR">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
    
    <body>
        <div style="text-align: center;">
            <img src="http://localhost/veterinaria/public/img/defaultCabecalhoFicha.png">
        </div>
        <h2 style="text-align: center;">EXAME CITOPATOLÓGICO</h2>
    
        <table>
            <tbody>
                <tr>
                    <td style="padding-right: 15px;"><b>Nome do Animal</b></td>
                    <td style="padding-right: 30px;">' . $DadosFicha->getAnimal()->getNome() . '</td>
    
                    <td style="padding-right: 15px;"><b>Espécie</b></td>
                    <td style="padding-right: 30px;">' . $DadosFicha->getAnimal()->getEspecie()->getDescricao() . '</td>
    
                    <td style="padding-right: 15px;"><b>Raça</b></td>
                    <td style="padding-right: 30px;">' . $DadosFicha->getAnimal()->getRaca()->getDescricao() . '</td>
                </tr>
                <tr>
                    <td><b>Idade </b></td>
                    <td>' . $DadosFicha->getIdadeAno() . '</td>
    
                    <td><b>Sexo </b></td>
                    <td>' . $DadosFicha->getAnimal()->getSexo() . '</td>
                </tr>
                <tr>
                    <td><b>Proprietário </b></td>
                    <td> ' . $DadosFicha->getAnimal()->getDono1()->getNome() . '</td>
                </tr>
                <tr>
                    <td><b>Veterinário </b></td>
                    <td> ' . $DadosFicha->getVeterinarioRemetente()->getNome() . '</td>
                </tr>
            </tbody>
        </table>
    
        <br>
    
        <span><b>Natureza do Material: </b></span> ' . $DadosFicha->getMaterialRecebido() . '
    
        <br><br>
    
        <p><b>Descricão Microscópica: </b></p>
    
        <br><br>
    
        <p><b>Diagnóstico/Conclusão: </b></p> ' . $DadosFicha->getDiagnostico() . '
    
        <br><br>
    
        <p><b>Notas: </b></p> 
    
        <br><br>
    
        <p><b>Referências: </b></p> 
    
        <br><br><br>
    
        <span style="font-size: small;">Observação: este laudo, como todo resultado de análise laboratorial, deve ser
            submetido à avaliação do médico veterinário responsável, junto aos demais exames e histórico do paciente.</span>
    
        <br>
    
        <div style="text-align: center;">
            <img src="http://localhost/veterinaria/public/img/AssClairton.png" width="150px">
        </div>
    
    </body>
    
    </html> ';
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
