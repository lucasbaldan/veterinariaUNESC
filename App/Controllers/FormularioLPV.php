<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FormularioLPV {

    public static function exibir(Request $request, Response $response){
        $response->getBody()->write(json_encode(["mensagem" => "Sucesso!"]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function Salvar(){
        extract($_POST);

        $flpv = new \App\Models\FormularioLPV($cdFichaLPV, $dtFicha, $animal, $nmVeterinarioRemetente, $crmvVeterinarioRemetente,$nrTelVeterinarioRemetente, $dsEmailVeterinarioRemetente, $nmCidadeVeterinarioRemetente, $cdUsuarioPlantonista, $nmProprietario, $nrTelefoneProprietario, $cidadePropriedade, $dsEspecie, $dsRaca, $dsSexo, $idade, $totalAnimais,$qtdAnimaisDoentes, $qtdAnimaisMortos, $dsMaterialRecebido, $dsDiagnosticoPresuntivo, $flAvaliacaoTumoralComMargem, $dsNomeAnimal, $dsEpidemiologiaHistoriaClinica, $dsLesoesMacroscopicas, $dsLesoesHistologicas, $dsDiagnostico, $dsRelatorio, $cdFichaLPV);

        if (empty($cdFichaLPV)){
            $flpv->Insert();
        }else{
            $flpv->Update();
        }
    }
}