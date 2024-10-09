<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Logs
{
    private $twig;
    private $TelaComMenus;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
        $this->TelaComMenus = \App\Views\TelaComMenus::getTelaComMenus($this->twig);
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $permissao = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LOGS', 'FL_ACESSAR');
        if (!$permissao) {
            return $this->twig->render($response, 'TelaBase.twig', [
                'versao' => $GLOBALS['versao'],
                'cssLinks' => 'TelaMenus.css',
                'conteudo_tela' => $this->TelaComMenus->renderTelaComMenus($this->twig->fetch('telaErro.twig')),
            ]);
        }

        $exibeDados = false;
        $table = '';
        $legendasTelas = [
            'FICHA_LPV' => 'Ficha LPV',
            'IMAGENS_ATENDIMENTOS' => 'Imagens Fichas LPV',
            'PESSOAS' => 'Pessoas',
            'USUARIOS' => 'Usuários',
            'GRUPOS_USUARIOS' => 'Grupos Usuários',
            'ANIMAIS' => 'Animais',
            'ESPECIES' => 'Espécies',
            'RACAS' => 'Raças',
            'CIDADES' => 'Cidades',
            'BAIRROS' => 'Bairros',
            'LOGRADOUROS' => 'Logradouros'
        ];

        $legendasAcao = [
            'UPDATE' => "<span style='color: blue;'>EDIÇÃO</span>",
            'INSERT' => "<span style='color: green;'>INSERÇÃO</span>",
            'DELETE' => "<span style='color: red;'>EXCLUSÃO</span>",
        ];

        $Formulario = $request->getParsedBody();
        $filtrar = !empty($Formulario['filtrar']) ? $Formulario['filtrar'] : '';
        $tabela = !empty($Formulario['tabela']) ? $Formulario['tabela'] : '';
        $codigo = !empty($Formulario['codigo']) ? $Formulario['codigo'] : '';
        $dtInicial = !empty($Formulario['dtInicial']) ? $Formulario['dtInicial'] : '';
        $dtFinal = !empty($Formulario['dtFinal']) ? $Formulario['dtFinal'] : '';

        $selectTabela =
            '<option value="">Selecione...</option>
        <option value="FICHA_LPV"' . ($tabela == 'FICHA_LPV' ? ' selected' : '') . '>Ficha LPV</option>
        <option value="IMAGENS_ATENDIMENTOS"' . ($tabela == 'IMAGENS_ATENDIMENTOS' ? ' selected' : '') . '>Imagens Fichas LPV</option>
        <option value="PESSOAS"' . ($tabela == 'PESSOAS' ? ' selected' : '') . '>Pessoas</option>
        <option value="USUARIOS"' . ($tabela == 'USUARIOS' ? ' selected' : '') . '>Usuários</option>
        <option value="GRUPOS_USUARIOS"' . ($tabela == 'GRUPOS_USUARIOS' ? ' selected' : '') . '>Grupos Usuários</option>
        <option value="ANIMAIS"' . ($tabela == 'ANIMAIS' ? ' selected' : '') . '>Animais</option>
        <option value="ESPECIES"' . ($tabela == 'ESPECIES' ? ' selected' : '') . '>Espécies</option>
        <option value="RACAS"' . ($tabela == 'RACAS' ? ' selected' : '') . '>Raças</option>
        <option value="CIDADES"' . ($tabela == 'CIDADES' ? ' selected' : '') . '>Cidades</option>
        <option value="BAIRROS"' . ($tabela == 'BAIRROS' ? ' selected' : '') . '>Bairros</option>
        <option value="LOGRADOUROS"' . ($tabela == 'LOGRADOUROS' ? ' selected' : '') . '>Logradouros</option>';


        if (!empty($filtrar)) {
            $exibeDados = true;

            $retorno = \App\Models\Logs::PesquisaFiltrada($tabela, $codigo, $dtInicial, $dtFinal);

            if (!empty($retorno)) {
                $table = "<table class='table table-striped' style='font-size: 14px;'>
                                <thead>
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th>Usuário</th>
                                        <th>Ação</th>
                                        <th>Tela</th>
                                        <th>Código</th>
                                        <th>Dados</th>
                                    </tr>
                                </thead>
                                <tbody> 
                ";
                foreach ($retorno as $value) {
                    $table .= "<tr>
                                <td style='vertical-align: middle;'>" . date('d/m/Y H:i:s', strtotime($value['DT_HORA'])) . "</td>
                                <td style='vertical-align: middle;'>" . $value['USUARIO'] . "</td>
                                <td style='vertical-align: middle;'>" . (isset($legendasAcao[$value['ACAO']]) ? $legendasAcao[$value['ACAO']] : $value['ACAO']) . "</td>
                                <td style='vertical-align: middle;'>" . (isset($legendasTelas[$value['TABELA']]) ? $legendasTelas[$value['TABELA']] : $value['TABELA']) . "</td>
                                <td style='vertical-align: middle;'>" . $value['CODIGO'] . "</td>
                                <td style='max-width: 300px;'> 
                                <div style='max-height: 200px; overflow: auto; display: block;'>" . $value['DADOS'] . "</td> </div>
                                </tr>";
                }

                $table .= "</tbody>
                            </table>";
            } else {
                $table = "<h6 class='text-center'>Nenhum dado retornado</h6>";
            }
        }

        $telaRelatoriovalues = $this->twig->fetch('logs.twig', [
            "selectTabela" => $selectTabela,
            "codigo" => $codigo,
            "dtInicial" => $dtInicial,
            "dtFinal" => $dtFinal,
            "exibeDados" => $exibeDados,
            "table" => $table
        ]);

        $conteudoTela = $this->TelaComMenus->renderTelaComMenus($telaRelatoriovalues);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => "TelaMenus.css;",
            // 'jsLinks' => "logs.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
