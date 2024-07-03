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
            $flAcessarFichaLPV = $permissoesArray['FICHA_LPV']['FL_ACESSAR'];
            $flEditarFichaLPV = $permissoesArray['FICHA_LPV']['FL_EDITAR'];
            $flInserirFichaLPV = $permissoesArray['FICHA_LPV']['FL_INSERIR'];
            $flExcluirFichaLPV = $permissoesArray['FICHA_LPV']['FL_EXCLUIR'];

            // Cadastro de Pessoas
            $flAcessarCadastroPessoas = $permissoesArray['CADASTRO_PESSOAS']['FL_ACESSAR'];
            $flEditarCadastroPessoas = $permissoesArray['CADASTRO_PESSOAS']['FL_EDITAR'];
            $flInserirCadastroPessoas = $permissoesArray['CADASTRO_PESSOAS']['FL_INSERIR'];
            $flExcluirCadastroPessoas = $permissoesArray['CADASTRO_PESSOAS']['FL_EXCLUIR'];

            // Cadastro de Usuários
            $flAcessarCadastroUsuarios = $permissoesArray['CADASTRO_USUARIOS']['FL_ACESSAR'];
            $flEditarCadastroUsuarios = $permissoesArray['CADASTRO_USUARIOS']['FL_EDITAR'];
            $flInserirCadastroUsuarios = $permissoesArray['CADASTRO_USUARIOS']['FL_INSERIR'];
            $flExcluirCadastroUsuarios = $permissoesArray['CADASTRO_USUARIOS']['FL_EXCLUIR'];

            // Controle de Acessos
            $flAcessarControleAcessos = $permissoesArray['CONTROLE_ACESSOS']['FL_ACESSAR'];
            $flEditarControleAcessos = $permissoesArray['CONTROLE_ACESSOS']['FL_EDITAR'];
            $flInserirControleAcessos = $permissoesArray['CONTROLE_ACESSOS']['FL_INSERIR'];
            $flExcluirControleAcessos = $permissoesArray['CONTROLE_ACESSOS']['FL_EXCLUIR'];
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
        }





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


        ]);

        $conteudoTela = $this->twig->fetch('TelaComMenus.twig', ['conteudo_tela' => $telaCadastroUsuarios]);

        return $this->twig->render($response, 'TelaBase.twig', [
            'cssLinks' => "TelaMenus.css;",
            'jsLinks' => "CadastroAcessosGruposUsuarios.js",
            'conteudo_tela' => $conteudoTela,
        ]);
    }
}
