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
