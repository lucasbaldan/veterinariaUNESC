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

        $itemAnimal = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('ANIMAL', 'FL_ACESSAR');
        $itemPessoa = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_PESSOAS', 'FL_ACESSAR');
        $itemAtendimento = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('FICHA_LPV', 'FL_ACESSAR');
        $itemEspecie = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('ESPECIE', 'FL_ACESSAR');
        $itemRaca = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('RACA', 'FL_ACESSAR');
        $itemLogradouro = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LOGRADOURO', 'FL_ACESSAR');
        $itemBairro = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('BAIRRO', 'FL_ACESSAR');
        $itemCidade = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('MUNICIPIO', 'FL_ACESSAR');
        $itemAcessoGrupo = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CONTROLE_ACESSOS', 'FL_ACESSAR');
        $itemGrupoUsuario = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_GRUPOS_USUARIOS', 'FL_ACESSAR');
        $itemUsuario = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CADASTRO_USUARIOS', 'FL_ACESSAR');
        $itemRelFichaLPV = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('RELATORIOS', 'FL_ACESSAR');
        $itemLogs = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('LOGS', 'FL_ACESSAR');

        // $itemAnimal = true;
        // $itemPessoa = true;
        // $itemAtendimento = true;
        // $itemEspecie = true;
        // $itemRaca = true;
        // $itemLogradouro = true;
        // $itemBairro = false;
        // $itemCidade = true;
        // $itemAcessoGrupo = true;
        // $itemGrupoUsuario = true;
        // $itemUsuario = true;
        // $itemRelFichaLPV = true;

        if ($itemEspecie || $itemRaca) $abaAnimal = true;
        else $abaAnimal = false;

        if ($itemBairro || $itemCidade || $itemLogradouro) $abaEndereco = true;
        else $abaEndereco = false;

        if ($abaAnimal || $abaEndereco) $abaCadastrosGerais = true;
        else $abaCadastrosGerais = false;

        if ($itemAcessoGrupo || $itemGrupoUsuario || $itemUsuario || $itemLogs) $abaControleAcesso = true;
        else $abaControleAcesso = false;

        if ($itemRelFichaLPV) $abaRelatorios = true;
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
            'itemLogs' => $itemLogs,

            'abaCadastrosGerais' => $abaCadastrosGerais,
            'abaAnimal' => $abaAnimal,
            'abaEndereco' => $abaEndereco,
            'abaControleAcesso' => $abaControleAcesso,
            'abaRelatorios' => $abaRelatorios,

        ]);
    }

    public static function getTelaComMenus($twig)
    {
        return new Self($twig);
    }
}
