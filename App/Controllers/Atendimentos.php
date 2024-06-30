<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Atendimentos
{

    public static function montarGrid(Request $request, Response $response)
    {

        try {

            $grid = $request->getParsedBody();

            $orderBy = isset($grid['order'][0]['column']) ? (int)$grid['order'][0]['column'] : '';
            if ($orderBy == 0) $orderBy = " ficha_lpv.CD_FICHA_LPV";
            if ($orderBy == 1) $orderBy = "ficha_lpv.DT_FICHA";
            if ($orderBy == 2) $orderBy = "tipo_animal.descricao";
            if ($orderBy == 3) $orderBy = "especies.descricao";
            if ($orderBy == 4) $orderBy = "racas.descricao";
            if ($orderBy == 5) $orderBy = "animais.sexo";
            if ($orderBy == 6) $orderBy = "dono.nm_pessoa";
            if ($orderBy == 7) $orderBy = "veterinario.nm_pessoa";
            if ($orderBy == 8) $orderBy = "cidades.nome";
            if ($orderBy == 9) $orderBy = "ficha_lpv.DS_MATERIAL_RECEBIDO";
            if ($orderBy == 10) $orderBy = "ficha_lpv.DS_DIAGNOSTICO_PRESUNTIVO";
            if ($orderBy == 11) $orderBy = "ficha_lpv.FL_AVALIACAO_TUMORAL_COM_MARGEM";
            if ($orderBy == 12) $orderBy = "ficha_lpv.DS_EPIDEMIOLOGIA_HISTORIA_CLINICA";
            if ($orderBy == 13) $orderBy = "ficha_lpv.DS_LESOES_MACROSCOPICAS";
            if ($orderBy == 14) $orderBy = "ficha_lpv.DS_LESOES_HISTOLOGICAS";
            if ($orderBy == 15) $orderBy = "ficha_lpv.DS_DIAGNOSTICO";
            if ($orderBy == 16) $orderBy = "ficha_lpv.DS_RELATORIO";

            $parametrosBusca = [
                "pesquisaCodigo" => !empty($grid['columns'][0]['search']['value']) ? $grid['columns'][0]['search']['value'] : '',
                "pesquisaDescricao" => !empty($grid['columns'][1]['search']['value']) ? $grid['columns'][1]['search']['value'] : '',
                "pesquisaTipoAnimal" => !empty($grid['columns'][2]['search']['value']) ? $grid['columns'][2]['search']['value'] : '',
                "pesquisaDono" => !empty($grid['columns'][3]['search']['value']) ? $grid['columns'][3]['search']['value'] : '',
                "pesquisaEspecie" => !empty($grid['columns'][4]['search']['value']) ? $grid['columns'][4]['search']['value'] : '',
                "pesquisaRaca" => !empty($grid['columns'][5]['search']['value']) ? $grid['columns'][5]['search']['value'] : '',
                "inicio" => $grid['start'],
                "limit" => $grid['length'],
                "orderBy" =>  $orderBy,
                "orderAscDesc" => isset($grid['order'][0]['dir']) ? $grid['order'][0]['dir'] : ''
            ];

            $dadosSelect = \App\Models\Atendimentos::SelectGrid($parametrosBusca);
            $dados = [
                "draw" => (int)$grid['draw'],
                "recordsTotal" => isset($dadosSelect[0]['total_table']) ? $dadosSelect[0]['total_table'] : 0,
                "recordsFiltered" => isset($dadosSelect[0]['total_filtered']) ? $dadosSelect[0]['total_filtered'] : 0,
                "data" => $dadosSelect
            ];


            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $dados];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function controlar(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();

            //INPUTUS DADOS FICHA ANIMAL
            $codigo = !empty($dadosForm['cdFichaLPV']) ? $dadosForm['cdFichaLPV'] : '';
            $data = !empty($dadosForm['dtFicha']) ? $dadosForm['dtFicha'] : '';
            $materialRecebido = !empty($dadosForm['dsMaterialRecebido']) ? $dadosForm['dsMaterialRecebido'] : '';
            $dsDiagnosticoPresuntivo = !empty($dadosForm['dsDiagnosticoPresuntivo']) ? $dadosForm['dsDiagnosticoPresuntivo'] : '';
            $flAvaliacaoTumoralComMargem = !empty($dadosForm['flAvaliacaoTumoralComMargem']) ? $dadosForm['flAvaliacaoTumoralComMargem'] : '';
            $dsEpidemiologia = !empty($dadosForm['dsEpidemiologiaHistoriaClinica']) ? $dadosForm['dsEpidemiologiaHistoriaClinica'] : '';
            $dsLesoesMacroscopicas = !empty($dadosForm['dsLesoesMacroscopicas']) ? $dadosForm['dsLesoesMacroscopicas'] : '';
            $dsLesoesHistologicas = !empty($dadosForm['dsLesoesHistologicas']) ? $dadosForm['dsLesoesHistologicas'] : '';
            $dsDiagnostico = !empty($dadosForm['dsDiagnostico']) ? $dadosForm['dsDiagnostico'] : '';
            $dsRelatorio = !empty($dadosForm['dsRelatorio']) ? $dadosForm['dsRelatorio'] : '';


            // INPUTS DO ANIMAL
            $codigoAnimal = !empty($dadosForm['cdAnimal']) ? $dadosForm['cdAnimal'] : '';
            $alterouAnimal = !empty($dadosForm['alterouAnimal']) ? $dadosForm['alterouAnimal'] : '';
            $nomeAnimal = isset($dadosForm['animal']) ? $dadosForm['animal'] : '';
            $tipoAnimal = isset($dadosForm['select2tipoAnimal']) ? $dadosForm['select2tipoAnimal'] : '';
            $especieAnimal = isset($dadosForm['select2especieAnimal']) ? $dadosForm['select2especieAnimal'] : '';
            $racaAnimal = isset($dadosForm['select2racaAnimal']) ? $dadosForm['select2racaAnimal'] : '';
            $sexoAnimal = isset($dadosForm['dsSexo']) ? $dadosForm['dsSexo'] : '';
            $idadeAnimal = isset($dadosForm['idade']) ? $dadosForm['idade'] : '';
            $anoNascimentoAnimal = isset($dadosForm['anoNascimento']) ? $dadosForm['anoNascimento'] : '';

            // INPUTS DA PESSOA DONA DO ANIMAL
            $donoNaoDeclarado = isset($dadosForm['donoNaoDeclarado']) ? 'S' : 'N';
            $alterouDono = !empty($dadosForm['alterouDono']) ? $dadosForm['alterouDono'] : '';
            $nomeDono = isset($dadosForm['nmProprietario']) ? $dadosForm['nmProprietario'] : '';
            $nrTelefoneDono = isset($dadosForm['nrTelefoneProprietario']) ? $dadosForm['nrTelefoneProprietario'] : '';

            // INPUTS DA PESSOA VETERINÁRIA
            $codigoVeterinario = !empty($dadosForm['cdVeterinarioRemetente']) ? $dadosForm['cdVeterinarioRemetente'] : '';
            $alterouVeterinario = !empty($dadosForm['alterouVeterinario']) ? $dadosForm['alterouVeterinario'] : '';
            $nomeVeterinario = isset($dadosForm['nmVeterinarioRemetente']) ? $dadosForm['nmVeterinarioRemetente'] : '';
            $crmvVeterinario = isset($dadosForm['crmvVeterinarioRemetente']) ? $dadosForm['crmvVeterinarioRemetente'] : '';
            $telefoneVeterinario = isset($dadosForm['nrTelVeterinarioRemetente']) ? $dadosForm['nrTelVeterinarioRemetente'] : '';
            $emailVeterinario = isset($dadosForm['dsEmailVeterinarioRemetente']) ? $dadosForm['dsEmailVeterinarioRemetente'] : '';
            $cdCidadeVeterinario = isset($dadosForm['select2cdCidadeVeterinario']) ? $dadosForm['select2cdCidadeVeterinario'] : '';

            if (empty($codigoAnimal)) throw new Exception("Houve um erro ao tentar processar a requisição <br><br> tente novamente mais tarde!");
            if (empty($codigoVeterinario)) throw new Exception("Selecione pelo menos um veterinário ao Atendimento para concluir o cadastro");

            $AnimalFicha = \App\Models\Animais::findById($codigoAnimal);

            if ($alterouDono == 'S') {
                if ($donoNaoDeclarado == 'N') {
                    $Dono = \App\Models\Pessoas::findById($AnimalFicha->getDono1()->getCodigo());
                    $Dono->setNome($nomeDono);
                    $Dono->setTelefone($nrTelefoneDono);
                    $Dono->Update();

                    if (!$Dono->getResult()) {
                        throw new Exception($Dono->getMessage());
                    }
                } else {
                    $AnimalFicha->setDonoNaoDeclarado('S');
                    $AnimalFicha->setDono1(null);
                }
            }

            if ($alterouAnimal == 'S') {
                $AnimalFicha->setNome($nomeAnimal);
                $AnimalFicha->setTipoAnimal($tipoAnimal);
                $AnimalFicha->setEspecie($especieAnimal);
                $AnimalFicha->setRaca($racaAnimal);
                $AnimalFicha->setSexo($sexoAnimal);
                $AnimalFicha->setIdade($idadeAnimal);
                $AnimalFicha->setAnoNascimento($anoNascimentoAnimal);

                $AnimalFicha->Atualizar();

                if (!$AnimalFicha->getResult()) {
                    throw new Exception($AnimalFicha->getMessage());
                }
            }

            if (empty($codigoVeterinario)) {
                $VeterinarioFicha = new \App\Models\Pessoas($nomeVeterinario, $cdCidadeVeterinario, $telefoneVeterinario, '', $emailVeterinario, $crmvVeterinario, '', '', 'S', '', '');
                $VeterinarioFicha->Insert();

                if (!$VeterinarioFicha->getResult()) {
                    throw new Exception($VeterinarioFicha->getMessage());
                }
            } else {
                $VeterinarioFicha = \App\Models\Pessoas::findById($codigoVeterinario);
                if ($alterouVeterinario == 'S') {
                    $VeterinarioFicha->setNome($nomeVeterinario);
                    $VeterinarioFicha->setCRMV($crmvVeterinario);
                    $VeterinarioFicha->setTelefone($telefoneVeterinario);
                    $VeterinarioFicha->setEmail($emailVeterinario);
                    $VeterinarioFicha->setCidade($cdCidadeVeterinario);

                    $VeterinarioFicha->Update();

                    if (!$VeterinarioFicha->getResult()) {
                        throw new Exception($VeterinarioFicha->getMessage());
                    }
                }
            }






            // $cad = new \App\Models\Animais($nome, $donoNaoDeclarado, $cdTipoAnimal, $cdEspecie, $cdRaca, $dsSexo, $idade, $anoNascimento, $dono = null, null, $codigo);
            // if (empty($codigo)) {
            //     $cad->Inserir();
            // } else {
            //     $cad->Atualizar();
            // }

            // if(!$cad->getResult()){
            //     throw new Exception($cad->getMessage());
            // }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function excluir(Request $request, Response $response)
    {
        try {
            $dadosForm = $request->getParsedBody();

            $codigo = !empty($dadosForm['cdAnimal']) ? $dadosForm['cdAnimal'] : '';

            if (empty($codigo)) {
                throw new Exception("Houve um erro ao processo a requisição<br>Tente novamente mais tarde");
            }

            $cad = new \App\Models\Animais('', '', '', '', '', '', '', '', '', '', $codigo);
            $cad->Excluir();


            if (!$cad->getResult()) {
                throw new Exception($cad->getMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => ''];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = 500;
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}
