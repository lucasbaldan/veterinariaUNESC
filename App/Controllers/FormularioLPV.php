<?php

namespace App\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FormularioLPV
{

    public static function Salvar(Request $request, Response $response)
    {

        try {
            $Formulario = $request->getParsedBody();

            $cdFichaLPV = !empty($Formulario['cdFichaLPV']) ? $Formulario['cdFichaLPV'] : '';
            $dtFicha = !empty($Formulario['dtFicha']) ? $Formulario['dtFicha'] : '';
            $animal = !empty($Formulario['animal']) ? $Formulario['animal'] : '';
            $nmVeterinarioRemetente = !empty($Formulario['nmVeterinarioRemetente']) ? $Formulario['nmVeterinarioRemetente'] : '';
            $crmvVeterinarioRemetente = !empty($Formulario['crmvVeterinarioRemetente']) ? $Formulario['crmvVeterinarioRemetente'] : '';
            $nrTelVeterinarioRemetente = !empty($Formulario['nrTelVeterinarioRemetente']) ? $Formulario['nrTelVeterinarioRemetente'] : '';
            $dsEmailVeterinarioRemetente = !empty($Formulario['dsEmailVeterinarioRemetente']) ? $Formulario['dsEmailVeterinarioRemetente'] : '';
            $nmCidadeVeterinarioRemetente = !empty($Formulario['nmCidadeVeterinarioRemetente']) ? $Formulario['nmCidadeVeterinarioRemetente'] : '';
            $cdUsuarioPlantonista = !empty($Formulario['cdUsuarioPlantonista']) ? $Formulario['cdUsuarioPlantonista'] : '';
            $nmProprietario = !empty($Formulario['nmProprietario']) ? $Formulario['nmProprietario'] : '';
            $nrTelefoneProprietario = !empty($Formulario['nrTelefoneProprietario']) ? $Formulario['nrTelefoneProprietario'] : '';
            $cidadePropriedade = !empty($Formulario['cidadePropriedade']) ? $Formulario['cidadePropriedade'] : '';
            $dsEspecie = !empty($Formulario['dsEspecie']) ? $Formulario['dsEspecie'] : '';
            $dsRaca = !empty($Formulario['dsRaca']) ? $Formulario['dsRaca'] : '';
            $dsSexo = !empty($Formulario['dsSexo']) ? $Formulario['dsSexo'] : '';
            $flCastrado = !empty($Formulario['flCastrado']) ? $Formulario['flCastrado'] : '';
            $idade = !empty($Formulario['idade']) ? $Formulario['idade'] : '';
            $totalAnimais = !empty($Formulario['totalAnimais']) ? $Formulario['totalAnimais'] : '';
            $qtdAnimaisDoentes = !empty($Formulario['qtdAnimaisDoentes']) ? $Formulario['qtdAnimaisDoentes'] : '';
            $qtdAnimaisMortos = !empty($Formulario['qtdAnimaisMortos']) ? $Formulario['qtdAnimaisMortos'] : '';
            $dsMaterialRecebido = !empty($Formulario['dsMaterialRecebido']) ? $Formulario['dsMaterialRecebido'] : '';
            $dsDiagnosticoPresuntivo = !empty($Formulario['dsDiagnosticoPresuntivo']) ? $Formulario['dsDiagnosticoPresuntivo'] : '';
            $flAvaliacaoTumoralComMargem = !empty($Formulario['flAvaliacaoTumoralComMargem']) ? $Formulario['flAvaliacaoTumoralComMargem'] : '';
            $tpAnimal = !empty($Formulario['tpAnimal']) ? $Formulario['tpAnimal'] : '';
            $dsEpidemiologiaHistoriaClinica = !empty($Formulario['dsEpidemiologiaHistoriaClinica']) ? $Formulario['dsEpidemiologiaHistoriaClinica'] : '';
            $dsLesoesMacroscopicas = !empty($Formulario['dsLesoesMacroscopicas']) ? $Formulario['dsLesoesMacroscopicas'] : '';
            $dsLesoesHistologicas = !empty($Formulario['dsLesoesHistologicas']) ? $Formulario['dsLesoesHistologicas'] : '';
            $dsDiagnostico = !empty($Formulario['dsDiagnostico']) ? $Formulario['dsDiagnostico'] : '';
            $dsRelatorio = !empty($Formulario['dsRelatorio']) ? $Formulario['dsRelatorio'] : '';

            // if (empty($login) || empty($senha)){
            //     throw new Exception("Preencha os campos Login e Senha.", 400);
            // }

            $flpv = new \App\Models\FormularioLPV($dtFicha, $animal, $nmVeterinarioRemetente, $crmvVeterinarioRemetente, $nrTelVeterinarioRemetente, $dsEmailVeterinarioRemetente, $nmCidadeVeterinarioRemetente, $cdUsuarioPlantonista, $nmProprietario, $nrTelefoneProprietario, $cidadePropriedade, $dsEspecie, $dsRaca, $dsSexo, $idade, $totalAnimais, $qtdAnimaisDoentes, $qtdAnimaisMortos, $dsMaterialRecebido, $dsDiagnosticoPresuntivo, $flAvaliacaoTumoralComMargem, $tpAnimal, $dsEpidemiologiaHistoriaClinica, $dsLesoesMacroscopicas, $dsLesoesHistologicas, $dsDiagnostico, $dsRelatorio, $cdFichaLPV);

            if (empty($cdFichaLPV)) {
                $retorno = $flpv->Insert();
            } else {
                $retorno = $flpv->Update();
            }

            if (!$flpv->GetResult()) {
                throw new Exception($flpv->GetMessage());
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $flpv->GetReturn()];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => 'OCORREU UM ERRO AO EFETUAR OPERACAO', "RETURN" => $e->getMessage()];
            $codigoHTTP = $e->getCode();
        }
        
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function RetornarFichasLPV(Request $request, Response $response)
    {

        try {


            $retorno = \App\Models\FormularioLPV::GeneralSearch('');

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar acessar os grupos de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    // public static function GerarRelatorioFichasLPV(Request $request, Response $response)
    // {

    //     try {

    //         $Formulario = $request->getParsedBody();

    //         $cdAnimal = !empty($Formulario['cdAnimal']) ? $Formulario['cdAnimal'] : '';
    //         $cdCidade = !empty($Formulario['cdCidade']) ? $Formulario['cdCidade'] : '';
    //         $cdVetRemetente = !empty($Formulario['cdVetRemetente']) ? $Formulario['cdVetRemetente'] : '';
    //         $dtInicialFicha = !empty($Formulario['dtInicialFicha']) ? $Formulario['dtInicialFicha'] : '';
    //         $dtFinalFicha = !empty($Formulario['dtFinalFicha']) ? $Formulario['dtFinalFicha'] : '';
    //         $flAvaliacaoTumoral = !empty($Formulario['flAvaliacaoTumoral']) ? $Formulario['flAvaliacaoTumoral'] : '';

    //         $retorno = \App\Models\FormularioLPV::RetornaFichasFiltradas($cdAnimal, $cdCidade, $cdVetRemetente, $dtInicialFicha, $dtFinalFicha, $flAvaliacaoTumoral);

    //         if (!$retorno) {
    //             throw new Exception("<b>Erro ao tentar acessar os grupos de usuários</b><br><br> Por favor, tente novamente.", 400);
    //         }

    //         $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
    //         $codigoHTTP = 200;
    //     } catch (Exception $e) {
    //         $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
    //         $codigoHTTP = $e->getCode();
    //     }
    //     $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
    //     return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    // }

    public static function RetornarDadosFichaLPV(Request $request, Response $response)
    {
        
        try {
            $Formulario = $request->getParsedBody();

            $cdFichaLPV = !empty($Formulario['cdFichaLPV']) ? $Formulario['cdFichaLPV'] : '';

            $retorno = \App\Models\FormularioLPV::RetornarDadosFichaLPV($cdFichaLPV);

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar acessar os grupos de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }

    public static function ApagarFichaLPV(Request $request, Response $response)
    {
        
        try {

            $Formulario = $request->getParsedBody();
            $cdFichaLPV = !empty($Formulario['cdFichaLPV']) ? $Formulario['cdFichaLPV'] : '';

            $retorno = \App\Models\FormularioLPV::Delete($cdFichaLPV);

            if (!$retorno) {
                throw new Exception("<b>Erro ao tentar acessar os grupos de usuários</b><br><br> Por favor, tente novamente.", 400);
            }

            $respostaServidor = ["RESULT" => TRUE, "MESSAGE" => '', "RETURN" => $retorno];
            $codigoHTTP = 200;
        } catch (Exception $e) {
            $respostaServidor = ["RESULT" => FALSE, "MESSAGE" => $e->getMessage(), "RETURN" => ''];
            $codigoHTTP = $e->getCode();
        }
        $response->getBody()->write(json_encode($respostaServidor, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($codigoHTTP)->withHeader('Content-Type', 'application/json');
    }
}
