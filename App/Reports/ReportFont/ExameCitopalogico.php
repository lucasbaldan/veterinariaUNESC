<?php

namespace App\Reports\ReportFont;

class ExameCitopalogico
{

    protected $html;
    private $idAtendimento;

    public function __construct($idAtendimento)
    {
        $this->idAtendimento = $idAtendimento;
    }

    public function generateReport()
    {
        $DadosFicha = \App\Models\Atendimentos::findById($this->idAtendimento);

        if (!$DadosFicha) $this->html = 'Erro interno ao gerar o documento';


        $this->html = '   <html lang="pt-BR">
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
    }
</style>

<body>
    <div style="text-align: center;">
        <img src="./../../../public/img/defaultCabecalhoFicha.png">
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
        <img src="./../../../public/img/AssClairton.png" width="150px">
    </div>

</body>

</html> ';
    }

    public function getHtml()
    {
        return $this->html;
    }
}
