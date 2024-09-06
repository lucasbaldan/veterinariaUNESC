<?php

namespace App\Reports;


use Dompdf\Dompdf;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class RelResultadoExamesPDF
{

    public static function gerar(Request $request, Response $response)
    {

        $conteudo = $request->getParsedBody();

        $dompdf = new Dompdf(array('enable_remote' => true));
        $cdFichaLPV = !empty($conteudo['cdFichaLPV']) ? $conteudo['cdFichaLPV'] : '';

        if (!empty($cdFichaLPV)) {
            $DadosFicha = \App\Models\Atendimentos::findById($cdFichaLPV);

            if (!$DadosFicha) $html = 'Erro interno ao gerar o documento';


            $html = ' <html lang="pt-BR">
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
    }
</style>

<body>
     <div style="text-align: center;">
            <img src="https://lpvunesc.com.br/veterinaria/public/img/defaultCabecalhoFicha.png" style="width: 600px;">
        </div>
    <h2 style="text-align: center;">Resultado de Exames</h2>

                <table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="width: 33.33%; text-align: left;"><b>Nome do Animal:</b> '.$DadosFicha->getAnimal()->getNome().'</td>
        <td style="width: 33.33%; text-align: left;"><b>Espécie:</b> '. $DadosFicha->getAnimal()->getEspecie()->getDescricao().'</td>
        <td style="width: 33.33%; text-align: left;"><b>Raça:</b> '. $DadosFicha->getAnimal()->getRaca()->getDescricao().'</td>
    </tr>
</table>

<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="width: 50%; text-align: left;"><b>Idade:</b> '.(empty($DadosFicha->getIdadeAno()) ? "" : $DadosFicha->getIdadeAno() . " Anos ") .
        (empty($DadosFicha->getIdadeMes()) ? "" : $DadosFicha->getIdadeMes() . " Meses ") .
        (empty($DadosFicha->getIdadeDia()) ? "" : $DadosFicha->getIdadeDia() . " Dias ").'</td>
        <td style="width: 50%; text-align: left;"><b>Sexo:</b> '. ($DadosFicha->getAnimal()->getSexo() == 'M' ? "Macho" : "Fêmea").'</td>
    </tr>
</table>
           
            <span><b>Tutor(a):</b>' . $DadosFicha->getAnimal()->getDono1()->getNome() . '</span>
            
            <br>

            <span><b>Clínica Veterinária:</b> Hospital Veterinário UNESC</span>
            
            <br>

            <span><b>Veterinário(a): </b>' . $DadosFicha->getVeterinarioRemetente()->getNome() . '</span>
            <span><b>  CRMV: </b>' . $DadosFicha->getVeterinarioRemetente()->getNrCRMV() . '</span>
            
            <br>
            
            <span><b>Data: </b>' . date('d/m/Y', strtotime($DadosFicha->getData())) . '<span>

    <br>


    <div style="text-align: center; font-size: 20px;">
        <span>RESULTADO DE EXAME NECROSCÓPICO</span>
    </div>

    <br>

    <div style="text-align: justify;">
        <span><b>Natureza do Material: </b></span> ' . $DadosFicha->getMaterialRecebido() . '
    </div>

    <br>

    <div style="text-align: justify;">
        <p><b>Descricão Macroscópica: </b></p> ' . $DadosFicha->getLessoesMacroscopias() . '
    </div>

    <div style="text-align: justify;">
        <p><b>Diagnóstico/Conclusão: </b></p> ' . $DadosFicha->getDiagnostico() . '
    </div>

    <br>

    <div style="text-align: justify;">
        <p><b>Notas: </b></p> ' . $DadosFicha->getRelatorio() . '
    </div>

    <br>

    <div style="text-align: justify;">
        <p><b>Referências: </b></p>
    </div>

    <br><br>

</body>

<footer>
    <div style="text-align: justify;">
        <span style="font-size: small;">Observação: este laudo, como todo resultado de análise laboratorial, deve ser
            submetido à avaliação do médico veterinário responsável, junto aos demais exames e histórico do
            paciente.</span>
    </div>

    <br>

    <div style="text-align: center;">
        <img src="https://lpvunesc.com.br/veterinaria/public/img/AssClairton.png" width="150px">
    </div>
</footer>

</html>';
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
