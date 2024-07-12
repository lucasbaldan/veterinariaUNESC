<?php

namespace App\Views;

use Slim\Views\Twig;
use DateTime;

class TelaComMenus
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function renderTelaComMenus($conteudoPrincipal)
    {

        $sessao = \App\Helpers\Sessao::getInfoSessao();
        if ($sessao == false) {
            return $this->twig->fetch('<h1> ERRO <h1>');
        }

        $sessao['userid'];

   $logout = $sessao['session_start_time'] + $GLOBALS['timesession'];
    $logoutDate = new DateTime();
    $logoutDate->setTimestamp($logout);

        $itemAnimal = true;
        $itemPessoa = true;
        $itemAtendimento = true;
        $itemEspecie = true;
        $itemRaca = true;
        $itemLogradouro = true;
        $itemBairro = false;
        $itemCidade = true;
        $itemAcessoGrupo = true;
        $itemGrupoUsuario = true;
        $itemUsuario = true;
        $itemRelFichaLPV = true;

        if($itemEspecie || $itemRaca) $abaAnimal = true;
        else $abaAnimal = false;

        if($itemBairro || $itemCidade || $itemLogradouro) $abaEndereco = true;
        else $abaEndereco = false;

        if($abaAnimal || $abaEndereco) $abaCadastrosGerais = true;
        else $abaCadastrosGerais = false;

        if($itemAcessoGrupo || $itemGrupoUsuario || $itemUsuario) $abaControleAcesso = true;
        else $abaControleAcesso = false;

        if($itemRelFichaLPV) $abaRelatorios = true;
        else $abaRelatorios = false;

        return $this->twig->fetch('TelaComMenus.twig', [
            'conteudo_tela' => $conteudoPrincipal,
            'nomeUsuario' => $sessao['username'],
            'tempoSessao' => $logoutDate->format('d/m/Y H:i'),

            'itemAnimal' => $itemAnimal,
            'itemPessoa' => $itemPessoa,
            'itemAtendimento' => $itemAtendimento,
            'itemEspecie' => $itemEspecie,
            'itemRaca' => $itemRaca,
            'itemLogradouro' => $itemLogradouro,
            'itemBairro' => $itemBairro,
            'itemCidade' => $itemCidade,
            'itemRelFichaLPV' => $itemRelFichaLPV,
            'itemAcessoGrupo' => $itemAcessoGrupo, 
            'itemGrupoUsuario' => $itemGrupoUsuario,
            'itemUsuario' => $itemUsuario, 

            'abaCadastrosGerais' => $abaCadastrosGerais,
            'abaAnimal' => $abaAnimal,
            'abaEndereco' => $abaEndereco,
            'abaControleAcesso' => $abaControleAcesso,
            'abaRelatorios' => $abaRelatorios,

        ]);
    }

    public static function getTelaComMenus($twig){
        return new Self($twig);
    }
}