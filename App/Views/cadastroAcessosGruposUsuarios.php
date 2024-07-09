<?php

namespace App\Views;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CadastroAcessosGruposUsuarios
{
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function exibir(Request $request, Response $response, $args)
    {
        $ajaxTela = $request->getParsedBody();

        $cdGrupoUsuarios = !empty($ajaxTela['id']) ? $ajaxTela['id'] : '';
        $grupoUsuarios = \App\Models\GruposUsuarios::findById($cdGrupoUsuarios);
        $exibeExcluir = true;
        $exibeSalvar = true;

        if (!empty($cdGrupoUsuarios)) {
            $cdGrupoUsuarios = $grupoUsuarios->GetCodigo();
            $nmGrupoUsuarios = $grupoUsuarios->GetNome();

            $permissoes = $grupoUsuarios->GetPermissoes();
            $permissoesArray = json_decode($permissoes, true);

            // Ficha LPV
            $flAcessarFichaLPV = !empty($permissoesArray['FICHA_LPV']['FL_ACESSAR']) ? $permissoesArray['FICHA_LPV']['FL_ACESSAR'] : 'N';
            $flEditarFichaLPV = !empty($permissoesArray['FICHA_LPV']['FL_EDITAR']) ? $permissoesArray['FICHA_LPV']['FL_EDITAR'] : 'N';
            $flInserirFichaLPV = !empty($permissoesArray['FICHA_LPV']['FL_INSERIR']) ? $permissoesArray['FICHA_LPV']['FL_INSERIR'] : 'N';
            $flExcluirFichaLPV = !empty($permissoesArray['FICHA_LPV']['FL_EXCLUIR']) ? $permissoesArray['FICHA_LPV']['FL_EXCLUIR'] : 'N';

            // Cadastro de Pessoas
            $flAcessarCadastroPessoas = !empty($permissoesArray['CADASTRO_PESSOAS']['FL_ACESSAR']) ? $permissoesArray['CADASTRO_PESSOAS']['FL_ACESSAR'] : 'N';
            $flEditarCadastroPessoas = !empty($permissoesArray['CADASTRO_PESSOAS']['FL_EDITAR']) ? $permissoesArray['CADASTRO_PESSOAS']['FL_EDITAR'] : 'N';
            $flInserirCadastroPessoas = !empty($permissoesArray['CADASTRO_PESSOAS']['FL_INSERIR']) ? $permissoesArray['CADASTRO_PESSOAS']['FL_INSERIR'] : 'N';
            $flExcluirCadastroPessoas = !empty($permissoesArray['CADASTRO_PESSOAS']['FL_EXCLUIR']) ? $permissoesArray['CADASTRO_PESSOAS']['FL_EXCLUIR'] : 'N';

            // Cadastro de Usuários
            $flAcessarCadastroUsuarios = !empty($permissoesArray['CADASTRO_USUARIOS']['FL_ACESSAR']) ? $permissoesArray['CADASTRO_USUARIOS']['FL_ACESSAR'] : 'N';
            $flEditarCadastroUsuarios = !empty($permissoesArray['CADASTRO_USUARIOS']['FL_EDITAR']) ? $permissoesArray['CADASTRO_USUARIOS']['FL_EDITAR'] : 'N';
            $flInserirCadastroUsuarios = !empty($permissoesArray['CADASTRO_USUARIOS']['FL_INSERIR']) ? $permissoesArray['CADASTRO_USUARIOS']['FL_INSERIR'] : 'N';
            $flExcluirCadastroUsuarios = !empty($permissoesArray['CADASTRO_USUARIOS']['FL_EXCLUIR']) ? $permissoesArray['CADASTRO_USUARIOS']['FL_EXCLUIR'] : 'N';

            // Controle de Acessos
            $flAcessarControleAcessos = !empty($permissoesArray['CONTROLE_ACESSOS']['FL_ACESSAR']) ? $permissoesArray['CONTROLE_ACESSOS']['FL_ACESSAR'] : 'N';
            $flEditarControleAcessos = !empty($permissoesArray['CONTROLE_ACESSOS']['FL_EDITAR']) ? $permissoesArray['CONTROLE_ACESSOS']['FL_EDITAR'] : 'N';
            $flInserirControleAcessos = !empty($permissoesArray['CONTROLE_ACESSOS']['FL_INSERIR']) ? $permissoesArray['CONTROLE_ACESSOS']['FL_INSERIR'] : 'N';
            $flExcluirControleAcessos = !empty($permissoesArray['CONTROLE_ACESSOS']['FL_EXCLUIR']) ? $permissoesArray['CONTROLE_ACESSOS']['FL_EXCLUIR'] : 'N';

            // ANIMAL
            $flAcessarAnimal = !empty($permissoesArray['ANIMAL']['FL_ACESSAR']) ? $permissoesArray['ANIMAL']['FL_ACESSAR'] : 'N';
            $flEditarAnimal = !empty($permissoesArray['ANIMAL']['FL_EDITAR']) ? $permissoesArray['ANIMAL']['FL_EDITAR'] : 'N';
            $flInserirAnimal = !empty($permissoesArray['ANIMAL']['FL_INSERIR']) ? $permissoesArray['ANIMAL']['FL_INSERIR'] : 'N';
            $flExcluirAnimal = !empty($permissoesArray['ANIMAL']['FL_EXCLUIR']) ? $permissoesArray['ANIMAL']['FL_EXCLUIR'] : 'N';

            // TIPO_ANIMAL
            $flAcessarTipoAnimal = !empty($permissoesArray['TIPO_ANIMAL']['FL_ACESSAR']) ? $permissoesArray['TIPO_ANIMAL']['FL_ACESSAR'] : 'N';
            $flEditarTipoAnimal = !empty($permissoesArray['TIPO_ANIMAL']['FL_EDITAR']) ? $permissoesArray['TIPO_ANIMAL']['FL_EDITAR'] : 'N';
            $flInserirTipoAnimal = !empty($permissoesArray['TIPO_ANIMAL']['FL_INSERIR']) ? $permissoesArray['TIPO_ANIMAL']['FL_INSERIR'] : 'N';
            $flExcluirTipoAnimal = !empty($permissoesArray['TIPO_ANIMAL']['FL_EXCLUIR']) ? $permissoesArray['TIPO_ANIMAL']['FL_EXCLUIR'] : 'N';

            // ESPECIE
            $flAcessarEspecie = !empty($permissoesArray['ESPECIE']['FL_ACESSAR']) ? $permissoesArray['ESPECIE']['FL_ACESSAR'] : 'N';
            $flEditarEspecie = !empty($permissoesArray['ESPECIE']['FL_EDITAR']) ? $permissoesArray['ESPECIE']['FL_EDITAR'] : 'N';
            $flInserirEspecie = !empty($permissoesArray['ESPECIE']['FL_INSERIR']) ? $permissoesArray['ESPECIE']['FL_INSERIR'] : 'N';
            $flExcluirEspecie = !empty($permissoesArray['ESPECIE']['FL_EXCLUIR']) ? $permissoesArray['ESPECIE']['FL_EXCLUIR'] : 'N';

            // RACA
            $flAcessarRaca = !empty($permissoesArray['RACA']['FL_ACESSAR']) ? $permissoesArray['RACA']['FL_ACESSAR'] : 'N';
            $flEditarRaca = !empty($permissoesArray['RACA']['FL_EDITAR']) ? $permissoesArray['RACA']['FL_EDITAR'] : 'N';
            $flInserirRaca = !empty($permissoesArray['RACA']['FL_INSERIR']) ? $permissoesArray['RACA']['FL_INSERIR'] : 'N';
            $flExcluirRaca = !empty($permissoesArray['RACA']['FL_EXCLUIR']) ? $permissoesArray['RACA']['FL_EXCLUIR'] : 'N';

            // MUNICIPIO
            $flAcessarMunicipio = !empty($permissoesArray['MUNICIPIO']['FL_ACESSAR']) ? $permissoesArray['MUNICIPIO']['FL_ACESSAR'] : 'N';
            $flEditarMunicipio = !empty($permissoesArray['MUNICIPIO']['FL_EDITAR']) ? $permissoesArray['MUNICIPIO']['FL_EDITAR'] : 'N';
            $flInserirMunicipio = !empty($permissoesArray['MUNICIPIO']['FL_INSERIR']) ? $permissoesArray['MUNICIPIO']['FL_INSERIR'] : 'N';
            $flExcluirMunicipio = !empty($permissoesArray['MUNICIPIO']['FL_EXCLUIR']) ? $permissoesArray['MUNICIPIO']['FL_EXCLUIR'] : 'N';

            // BAIRRO
            $flAcessarBairro = !empty($permissoesArray['BAIRRO']['FL_ACESSAR']) ? $permissoesArray['BAIRRO']['FL_ACESSAR'] : 'N';
            $flEditarBairro = !empty($permissoesArray['BAIRRO']['FL_EDITAR']) ? $permissoesArray['BAIRRO']['FL_EDITAR'] : 'N';
            $flInserirBairro = !empty($permissoesArray['BAIRRO']['FL_INSERIR']) ? $permissoesArray['BAIRRO']['FL_INSERIR'] : 'N';
            $flExcluirBairro = !empty($permissoesArray['BAIRRO']['FL_EXCLUIR']) ? $permissoesArray['BAIRRO']['FL_EXCLUIR'] : 'N';

            // LOGRADOURO
            $flAcessarLogradouro = !empty($permissoesArray['LOGRADOURO']['FL_ACESSAR']) ? $permissoesArray['LOGRADOURO']['FL_ACESSAR'] : 'N';
            $flEditarLogradouro = !empty($permissoesArray['LOGRADOURO']['FL_EDITAR']) ? $permissoesArray['LOGRADOURO']['FL_EDITAR'] : 'N';
            $flInserirLogradouro = !empty($permissoesArray['LOGRADOURO']['FL_INSERIR']) ? $permissoesArray['LOGRADOURO']['FL_INSERIR'] : 'N';
            $flExcluirLogradouro = !empty($permissoesArray['LOGRADOURO']['FL_EXCLUIR']) ? $permissoesArray['LOGRADOURO']['FL_EXCLUIR'] : 'N';

            // RELATORIOS
            $flAcessarRelatorios = !empty($permissoesArray['RELATORIOS']['FL_ACESSAR']) ? $permissoesArray['RELATORIOS']['FL_ACESSAR'] : 'N';
            $flEditarRelatorios = !empty($permissoesArray['RELATORIOS']['FL_EDITAR']) ? $permissoesArray['RELATORIOS']['FL_EDITAR'] : 'N';
            $flInserirRelatorios = !empty($permissoesArray['RELATORIOS']['FL_INSERIR']) ? $permissoesArray['RELATORIOS']['FL_INSERIR'] : 'N';
            $flExcluirRelatorios = !empty($permissoesArray['RELATORIOS']['FL_EXCLUIR']) ? $permissoesArray['RELATORIOS']['FL_EXCLUIR'] : 'N';
        } else {
            $cdGrupoUsuarios = '';
            $nmGrupoUsuarios = '';
            $exibeExcluir = false;

            // Ficha LPV
            $flAcessarFichaLPV = 'N';
            $flEditarFichaLPV = 'N';
            $flInserirFichaLPV = 'N';
            $flExcluirFichaLPV = 'N';

            // Cadastro de Pessoas
            $flAcessarCadastroPessoas = 'N';
            $flEditarCadastroPessoas = 'N';
            $flInserirCadastroPessoas = 'N';
            $flExcluirCadastroPessoas = 'N';

            // Cadastro de Usuários
            $flAcessarCadastroUsuarios = 'N';
            $flEditarCadastroUsuarios = 'N';
            $flInserirCadastroUsuarios = 'N';
            $flExcluirCadastroUsuarios = 'N';

            // Controle de Acessos
            $flAcessarControleAcessos = 'N';
            $flEditarControleAcessos = 'N';
            $flInserirControleAcessos = 'N';
            $flExcluirControleAcessos = 'N';

            // ANIMAL
            $flAcessarAnimal = 'N';
            $flEditarAnimal = 'N';
            $flInserirAnimal = 'N';
            $flExcluirAnimal = 'N';

            // TIPO_ANIMAL
            $flAcessarTipoAnimal = 'N';
            $flEditarTipoAnimal = 'N';
            $flInserirTipoAnimal = 'N';
            $flExcluirTipoAnimal = 'N';

            // ESPECIE
            $flAcessarEspecie = 'N';
            $flEditarEspecie = 'N';
            $flInserirEspecie = 'N';
            $flExcluirEspecie = 'N';

            // RACA
            $flAcessarRaca = 'N';
            $flEditarRaca = 'N';
            $flInserirRaca = 'N';
            $flExcluirRaca = 'N';

            // MUNICIPIO
            $flAcessarMunicipio = 'N';
            $flEditarMunicipio = 'N';
            $flInserirMunicipio = 'N';
            $flExcluirMunicipio = 'N';

            // BAIRRO
            $flAcessarBairro = 'N';
            $flEditarBairro = 'N';
            $flInserirBairro = 'N';
            $flExcluirBairro = 'N';

            // LOGRADOURO
            $flAcessarLogradouro = 'N';
            $flEditarLogradouro = 'N';
            $flInserirLogradouro = 'N';
            $flExcluirLogradouro = 'N';

            // RELATORIOS
            $flAcessarRelatorios = 'N';
            $flEditarRelatorios = 'N';
            $flInserirRelatorios = 'N';
            $flExcluirRelatorios = 'N';
        }


        $permissaoSalvar = \App\Controllers\GruposUsuarios::VerificaAcessosSemRequisicao('CONTROLE_ACESSOS', 'FL_EDITAR');
        $exibeSalvar = $permissaoSalvar == true ? true : false;

        $telaCadastroUsuarios = $this->twig->fetch('cadastroAcessosGruposUsuarios.twig', [
            'cdGrupoUsuarios' => $cdGrupoUsuarios,
            'nmGrupoUsuarios' => $nmGrupoUsuarios,
            'exibeSalvar' => $exibeSalvar,

            // Ficha LPV
            'flAcessarFichaLPV' => $flAcessarFichaLPV == 'S' ? 'checked' : '',
            'flEditarFichaLPV' => $flEditarFichaLPV == 'S' ? 'checked' : '',
            'flInserirFichaLPV' => $flInserirFichaLPV == 'S' ? 'checked' : '',
            'flExcluirFichaLPV' => $flExcluirFichaLPV == 'S' ? 'checked' : '',

            // Cadastro de Pessoas
            'flAcessarCadastroPessoas' => $flAcessarCadastroPessoas == 'S' ? 'checked' : '',
            'flEditarCadastroPessoas' => $flEditarCadastroPessoas == 'S' ? 'checked' : '',
            'flInserirCadastroPessoas' => $flInserirCadastroPessoas == 'S' ? 'checked' : '',
            'flExcluirCadastroPessoas' => $flExcluirCadastroPessoas == 'S' ? 'checked' : '',

            // Cadastro de Usuários
            'flAcessarCadastroUsuarios' => $flAcessarCadastroUsuarios == 'S' ? 'checked' : '',
            'flEditarCadastroUsuarios' => $flEditarCadastroUsuarios == 'S' ? 'checked' : '',
            'flInserirCadastroUsuarios' => $flInserirCadastroUsuarios == 'S' ? 'checked' : '',
            'flExcluirCadastroUsuarios' => $flExcluirCadastroUsuarios == 'S' ? 'checked' : '',

            // Controle de Acessos
            'flAcessarControleAcessos' => $flAcessarControleAcessos == 'S' ? 'checked' : '',
            'flEditarControleAcessos' => $flEditarControleAcessos == 'S' ? 'checked' : '',
            'flInserirControleAcessos' => $flInserirControleAcessos == 'S' ? 'checked' : '',
            'flExcluirControleAcessos' => $flExcluirControleAcessos == 'S' ? 'checked' : '',

            // ANIMAL
            'flAcessarAnimal' => $flAcessarAnimal == 'S' ? 'checked' : '',
            'flEditarAnimal' => $flEditarAnimal == 'S' ? 'checked' : '',
            'flInserirAnimal' => $flInserirAnimal == 'S' ? 'checked' : '',
            'flExcluirAnimal' => $flExcluirAnimal == 'S' ? 'checked' : '',

            // TIPO_ANIMAL
            'flAcessarTipoAnimal' => $flAcessarTipoAnimal == 'S' ? 'checked' : '',
            'flEditarTipoAnimal' => $flEditarTipoAnimal == 'S' ? 'checked' : '',
            'flInserirTipoAnimal' => $flInserirTipoAnimal == 'S' ? 'checked' : '',
            'flExcluirTipoAnimal' => $flExcluirTipoAnimal == 'S' ? 'checked' : '',

            // ESPECIE
            'flAcessarEspecie' => $flAcessarEspecie == 'S' ? 'checked' : '',
            'flEditarEspecie' => $flEditarEspecie == 'S' ? 'checked' : '',
            'flInserirEspecie' => $flInserirEspecie == 'S' ? 'checked' : '',
            'flExcluirEspecie' => $flExcluirEspecie == 'S' ? 'checked' : '',

            // RACA
            'flAcessarRaca' => $flAcessarRaca == 'S' ? 'checked' : '',
            'flEditarRaca' => $flEditarRaca == 'S' ? 'checked' : '',
            'flInserirRaca' => $flInserirRaca == 'S' ? 'checked' : '',
            'flExcluirRaca' => $flExcluirRaca == 'S' ? 'checked' : '',

            // MUNICIPIO
            'flAcessarMunicipio' => $flAcessarMunicipio == 'S' ? 'checked' : '',
            'flEditarMunicipio' => $flEditarMunicipio == 'S' ? 'checked' : '',
            'flInserirMunicipio' => $flInserirMunicipio == 'S' ? 'checked' : '',
            'flExcluirMunicipio' => $flExcluirMunicipio == 'S' ? 'checked' : '',

            // BAIRRO
            'flAcessarBairro' => $flAcessarBairro == 'S' ? 'checked' : '',
            'flEditarBairro' => $flEditarBairro == 'S' ? 'checked' : '',
            'flInserirBairro' => $flInserirBairro == 'S' ? 'checked' : '',
            'flExcluirBairro' => $flExcluirBairro == 'S' ? 'checked' : '',

            // LOGRADOURO
            'flAcessarLogradouro' => $flAcessarLogradouro == 'S' ? 'checked' : '',
            'flEditarLogradouro' => $flEditarLogradouro == 'S' ? 'checked' : '',
            'flInserirLogradouro' => $flInserirLogradouro == 'S' ? 'checked' : '',
            'flExcluirLogradouro' => $flExcluirLogradouro == 'S' ? 'checked' : '',

            // RELATORIOS
            'flAcessarRelatorios' => $flAcessarRelatorios == 'S' ? 'checked' : '',
            'flEditarRelatorios' => $flEditarRelatorios == 'S' ? 'checked' : '',
            'flInserirRelatorios' => $flInserirRelatorios == 'S' ? 'checked' : '',
            'flExcluirRelatorios' => $flExcluirRelatorios == 'S' ? 'checked' : '',

        ]);

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $telaCadastroUsuarios]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'versao' => $GLOBALS['versao'],
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "CadastroAcessosGruposUsuarios.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
