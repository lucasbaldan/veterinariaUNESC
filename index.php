<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Middleware\SessionMiddleware;


require __DIR__ . '/vendor/autoload.php';

$config = json_decode(file_get_contents(__DIR__ . '/App/Configurations.json'), true);

foreach ($config as $key => $value) {
    $GLOBALS[$key] = $value;
}
date_default_timezone_set('America/Sao_Paulo');


$app = AppFactory::create();
$app->setBasePath('/veterinaria');
$app->addRoutingMiddleware();
$app->addErrorMiddleware($GLOBALS['desenvolvimento'], true, true);

$sessionMiddleware = new SessionMiddleware();

$twig = Twig::create(__DIR__ . '/App/Views/paginas', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));



/////// ROTAS PARA REQUISIÇÕES QUE NÃO EXIGEM AUTENTICAÇÃO DE SESSÃO
$app->group('/paginas', function (RouteCollectorProxy $group) use ($twig) {

    $group->get('/login', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\LoginPage($twig);
        return $tela->exibir($request, $response, $args);
    });
})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, [
        '/veterinaria/paginas/login',
    ])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

$app->group('/server', function (RouteCollectorProxy $group) {

    $group->group('/usuarios', function (RouteCollectorProxy $usuariosGroup) {
        $usuariosGroup->post('/efetuarLogin',  App\Controllers\Usuarios::class . ':efetuarLogin');
        $usuariosGroup->post('/deslogar',  App\Helpers\Sessao::class . ':encerrarSessao');
    });
})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, [
        '/veterinaria/server/usuarios/efetuarLogin',
        '/veterinaria/server/usuarios/deslogar'
    ])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});


/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE TELAS COM SESSÃO

$app->get('/', function ($request, $response, array $args) {
    return $response->withHeader('Location', '/veterinaria/paginas/inicial')->withStatus(302);
});
$app->group('/paginas', function (RouteCollectorProxy $group) use ($twig) {

    $group->post('/fichaLPV', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\FormularioLPV($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/inicial', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\InicialPage($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listEspecie', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\listEspecie($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listRaca', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\listRaca($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listMunicipio', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\listMunicipio($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listBairro', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\listBairro($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listLogradouro', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\listLogradouro($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroPessoas', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroPessoas($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroUsuarios', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroUsuarios($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/gruposUsuarios', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\ListGruposUsuarios($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listAcessosGruposUsuarios', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\ListAcessosGruposUsuarios($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroAcessosGruposUsuarios', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroAcessosGruposUsuarios($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroGruposUsuarios', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroGruposUsuariosModal($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listPessoas', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\ListPessoas($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listAnimais', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\listAnimais($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listAtendimentos', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\listAtendimentos($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listUsuarios', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\ListUsuarios($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroAnimais', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroAnimais($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/relatorioFichaLPV', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\RelatorioFichaLPV($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroGruposUsuariosNovo', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroGruposUsuariosNovo($twig);
        return $tela->exibir($request, $response, $args);
    });

})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, [
        '/veterinaria/paginas',
        '/veterinaria/paginas/fichaLPV',
        '/veterinaria/paginas/inicial',
        '/veterinaria/paginas/listEspecie',
        '/veterinaria/paginas/listRaca',
        '/veterinaria/paginas/listMunicipio',
        '/veterinaria/paginas/listBairro',
        '/veterinaria/paginas/listLogradouro',
        '/veterinaria/paginas/cadastroPessoas',
        '/veterinaria/paginas/cadastroUsuarios',
        '/veterinaria/paginas/cadastroAnimais',
        '/veterinaria/paginas/gruposUsuarios',
        '/veterinaria/paginas/listAcessosGruposUsuarios',
        '/veterinaria/paginas/cadastroAcessosGruposUsuarios',
        '/veterinaria/paginas/cadastroGruposUsuarios',
        '/veterinaria/paginas/listPessoas',
        '/veterinaria/paginas/listAnimais',
        '/veterinaria/paginas/listAtendimentos',
        '/veterinaria/paginas/listUsuarios',
        '/veterinaria/paginas/relatorioFichaLPV',
        '/veterinaria/paginas/cadastroGruposUsuariosNovo',
    ])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
})->add($sessionMiddleware);


/////////////////////// ROTAS DE REQUISIÇÕES PARA CARREGAMENTO DINÂMICO DE MODAIS
$app->group('/modais', function (RouteCollectorProxy $group) use ($twig) {

    $group->post('/cadastroEspecie', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroEspecieModal($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroRaca', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroRacaModal($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroMunicipio', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroMunicipioModal($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroBairro', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroBairroModal($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroLogradouro', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroLogradouroModal($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/buscaRapidaPessoa', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\BuscaRapidaPessoa($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/buscaRapidaAnimal', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\BuscaRapidaAnimal($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/cadastroGruposUsuarios', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroGruposUsuariosModal($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/recarregarGaleria', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\recarregarGaleria($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->post('/resetarSenha', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\resetarSenha($twig);
        return $tela->exibir($request, $response, $args);
    });

})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, [
        '/veterinaria/modais/cadastroRaca',
        '/veterinaria/modais/cadastroEspecie',
        '/veterinaria/modais/cadastroMunicipio',
        '/veterinaria/modais/cadastroLogradouro',
        '/veterinaria/modais/cadastroBairro',
        '/veterinaria/modais/buscaRapidaAnimal',
        '/veterinaria/modais/buscaRapidaPessoa',
        '/veterinaria/modais/cadastroGruposUsuarios',
        '/veterinaria/modais/recarregarGaleria',
        '/veterinaria/modais/resetarSenha',
    ])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
})->add($sessionMiddleware);

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE BACKEND

$app->group('/server', function (RouteCollectorProxy $group) {

    $group->group('/relatorios', function (RouteCollectorProxy $Group) {
        $Group->post('/fichaLPV', App\Reports\RelFichaLPV::class . ':gerar');
    });

    $group->group('/pessoas', function (RouteCollectorProxy $pessoasGroup) {
        $pessoasGroup->post('/controlar', App\Controllers\Pessoas::class . ':Salvar');
        $pessoasGroup->post('/retornaPesquisaModal', App\Controllers\Pessoas::class . ':retornaPesquisaModal');
        $pessoasGroup->post('/selecionarPessoa', App\Controllers\Pessoas::class . ':RetornarDadosPessoa');
        $pessoasGroup->post('/atualizaExclusaoPessoa', App\Controllers\Pessoas::class . ':AtualizarExclusaoPessoa');
        $pessoasGroup->post('/excluiPessoa', App\Controllers\Pessoas::class . ':ApagarPessoa');
        $pessoasGroup->post('/grid', App\Controllers\Pessoas::class . ':montarGrid');
        $pessoasGroup->post('/general', App\Controllers\Pessoas::class . ':General');
    });

    $group->group('/especie', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\Especies::class . ':montarGrid');
        $Group->post('/controlar', App\Controllers\Especies::class . ':controlar');
        $Group->post('/excluir', App\Controllers\Especies::class . ':excluir');
        $Group->post('/general', App\Controllers\Especies::class . ':general');
    });

    $group->group('/raca', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\Raças::class . ':montarGrid');
        $Group->post('/controlar', App\Controllers\Raças::class . ':controlar');
        $Group->post('/excluir', App\Controllers\Raças::class . ':excluir');
        $Group->post('/general', App\Controllers\Raças::class . ':general');
    });

    $group->group('/municipio', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\Municipios::class . ':montarGrid');
        $Group->post('/controlar', App\Controllers\Municipios::class . ':controlar');
        $Group->post('/excluir', App\Controllers\Municipios::class . ':excluir');
        $Group->post('/general', App\Controllers\Municipios::class . ':general');
    });

    $group->group('/bairro', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\Bairros::class . ':montarGrid');
        $Group->post('/controlar', App\Controllers\Bairros::class . ':controlar');
        $Group->post('/excluir', App\Controllers\Bairros::class . ':excluir');
        $Group->post('/general', App\Controllers\Bairros::class . ':general');
    });

    $group->group('/logradouro', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\Logradouros::class . ':montarGrid');
        $Group->post('/controlar', App\Controllers\Logradouros::class . ':controlar');
        $Group->post('/excluir', App\Controllers\Logradouros::class . ':excluir');
        $Group->post('/general', App\Controllers\Logradouros::class . ':general');
    });

    $group->group('/animais', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\Animais::class . ':montarGrid');
        $Group->post('/controlar', App\Controllers\Animais::class . ':controlar');
        $Group->post('/excluir', App\Controllers\Animais::class . ':excluir');
        $Group->post('/retornaPesquisaModal', App\Controllers\Animais::class . ':retornaPesquisaModal');
    });

    $group->group('/atendimentos', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\Atendimentos::class . ':montarGrid');
        $Group->post('/controlar', App\Controllers\Atendimentos::class . ':controlar');
        $Group->post('/excluir', App\Controllers\Atendimentos::class . ':excluir');
        $Group->post('/gerarCSV', App\Controllers\Atendimentos::class . ':gerarCSVGrid');
        $Group->post('/uploadGaleria', App\Controllers\Atendimentos::class . ':uploadGaleria');
        $Group->post('/excluirImagem', App\Controllers\Atendimentos::class . ':excluirGaleria');
    });

    $group->group('/estado', function (RouteCollectorProxy $Group) {
        $Group->post('/general', App\Controllers\Estados::class . ':general');
    });

    $group->group('/gruposUsuarios', function (RouteCollectorProxy $GrUsuariosGroup) {
        $GrUsuariosGroup->post('/salvaGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':Salvar');
        $GrUsuariosGroup->post('/excluiGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':ExcluirGruposUsuarios');
        $GrUsuariosGroup->post('/retornaGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarGruposUsuarios');
        $GrUsuariosGroup->post('/retornaDadosGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarDadosGrupoUsuario');
        $GrUsuariosGroup->post('/general',  App\Controllers\GruposUsuarios::class . ':General');
        $GrUsuariosGroup->post('/grid',  App\Controllers\GruposUsuarios::class . ':MontarGrid');
        $GrUsuariosGroup->post('/salvaAcessos',  App\Controllers\GruposUsuarios::class . ':GestaoAcessos');
        $GrUsuariosGroup->post('/verificaAcessos',  App\Controllers\GruposUsuarios::class . ':VerificaAcessos');
    });

    $group->group('/usuarios', function (RouteCollectorProxy $usuariosGroup) {
        $usuariosGroup->post('/salvaUsuario',  App\Controllers\Usuarios::class . ':Salvar');
        $usuariosGroup->post('/excluiUsuario',  App\Controllers\Usuarios::class . ':ExcluirUsuario');
        $usuariosGroup->post('/retornaUsuarios',  App\Controllers\Usuarios::class . ':RetornarUsuarios');
        $usuariosGroup->post('/retornaDadosUsuario',  App\Controllers\Usuarios::class . ':RetornarDadosUsuario');
        $usuariosGroup->post('/grid',  App\Controllers\Usuarios::class . ':montarGrid');
        $usuariosGroup->post('/alterarSenha',  App\Controllers\Usuarios::class . ':alterarSenha');
    });

    $group->group('/pdf', function (RouteCollectorProxy $pdf) {
        $pdf->post('/geraPdf',  App\Reports\RelFichaLPV::class . ':GerarPdf');
    });

    $group->group('/fichaLPV', function (RouteCollectorProxy $fichaLPVGroup) {
        // $fichaLPVGroup->post('/salvafichaLPV',  App\Controllers\FormularioLPV::class . ':Salvar');
        // $fichaLPVGroup->post('/retornaFichasLPV',  App\Controllers\FormularioLPV::class . ':RetornarFichasLPV');
        // $fichaLPVGroup->post('/retornaDadosFichaLPV',  App\Controllers\FormularioLPV::class . ':RetornarDadosFichaLPV');
        // $fichaLPVGroup->post('/apagaFichaLPV',  App\Controllers\FormularioLPV::class . ':ApagarFichaLPV');
        $fichaLPVGroup->post('/relatorioFichaLPV',  App\Controllers\FormularioLPV::class . ':GerarRelatorioFichasLPV');
    });
})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, [
        
        '/veterinaria/server/relatorios/fichaLPV',

        '/veterinaria/server/pessoas/controlar',
        '/veterinaria/server/pessoas/retornaPesquisaModal',
        '/veterinaria/server/pessoas/selecionarPessoa',
        '/veterinaria/server/pessoas/atualizaExclusaoPessoa',
        '/veterinaria/server/pessoas/excluiPessoa',
        '/veterinaria/server/pessoas/grid',
        '/veterinaria/server/pessoas/general',

        '/veterinaria/server/especie/grid',
        '/veterinaria/server/especie/controlar',
        '/veterinaria/server/especie/excluir',
        '/veterinaria/server/especie/general',

        '/veterinaria/server/raca/grid',
        '/veterinaria/server/raca/controlar',
        '/veterinaria/server/raca/excluir',
        '/veterinaria/server/raca/general',

        '/veterinaria/server/municipio/grid',
        '/veterinaria/server/municipio/controlar',
        '/veterinaria/server/municipio/excluir',
        '/veterinaria/server/municipio/general',

        '/veterinaria/server/bairro/grid',
        '/veterinaria/server/bairro/controlar',
        '/veterinaria/server/bairro/excluir',
        '/veterinaria/server/bairro/general',

        '/veterinaria/server/logradouro/grid',
        '/veterinaria/server/logradouro/controlar',
        '/veterinaria/server/logradouro/excluir',
        '/veterinaria/server/logradouro/general',

        '/veterinaria/server/estado/general',

        '/veterinaria/server/animais/grid',
        '/veterinaria/server/animais/controlar',
        '/veterinaria/server/animais/excluir',
        '/veterinaria/server/animais/retornaPesquisaModal',

        '/veterinaria/server/atendimentos/grid',
        '/veterinaria/server/atendimentos/controlar',
        '/veterinaria/server/atendimentos/excluir',
        '/veterinaria/server/atendimentos/gerarCSV',
        '/veterinaria/server/atendimentos/uploadGaleria',
        '/veterinaria/server/atendimentos/excluirImagem',

        '/veterinaria/server/gruposUsuarios/salvaGrupoUsuarios',
        '/veterinaria/server/gruposUsuarios/excluiGruposUsuarios',
        '/veterinaria/server/gruposUsuarios/retornaGruposUsuarios',
        '/veterinaria/server/gruposUsuarios/retornaDadosGrupoUsuarios',
        '/veterinaria/server/gruposUsuarios/general',
        '/veterinaria/server/gruposUsuarios/grid',
        '/veterinaria/server/gruposUsuarios/salvaAcessos',
        '/veterinaria/server/gruposUsuarios/verificaAcessos',

        '/veterinaria/server/usuarios/salvaUsuario',
        '/veterinaria/server/usuarios/excluiUsuario',
        '/veterinaria/server/usuarios/retornaUsuarios',
        '/veterinaria/server/usuarios/retornaDadosUsuario',
        '/veterinaria/server/usuarios/grid',
        '/veterinaria/server/usuarios/alterarSenha',

        '/veterinaria/server/fichaLPV/salvafichaLPV',
        '/veterinaria/server/fichaLPV/retornaFichasLPV',
        '/veterinaria/server/fichaLPV/retornaDadosFichaLPV',
        '/veterinaria/server/fichaLPV/apagaFichaLPV',
        '/veterinaria/server/fichaLPV/relatorioFichaLPV',

        '/veterinaria/server/pdf/geraPdf',
    ])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
})->add($sessionMiddleware);

$app->group('/server', function (RouteCollectorProxy $group) {

    $group->group('/midia', function (RouteCollectorProxy $Group) {
        $Group->get('/atendimento/{filename}', function (Request $request, Response $response, array $args) {
            $filename = $args['filename'];
            $filePath = __DIR__ . '/App/Assets/imagens/imagens_atendimento/' . $filename;

            if (!file_exists($filePath)) {
                return $response->withStatus(404);
            }

            $response->getBody()->write(file_get_contents($filePath));
            return $response->withHeader('Content-Type', mime_content_type($filePath));
        });
    });
})->add($sessionMiddleware);

// $app->group('/gruposUsuarios', function (RouteCollectorProxy $group) {

//     $group->post('/salvaGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':Salvar');

//     $group->post('/excluiGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':ExcluirGruposUsuarios');

//     $group->post('/retornaGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarGruposUsuarios');

//     $group->post('/retornaDadosGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarDadosGrupoUsuario');

// })->add(function (Request $request, RequestHandlerInterface $handler) {
//     $uri = $request->getUri()->getPath();
//     if (!in_array($uri, ['/veterinaria/gruposUsuarios/salvaGrupoUsuarios', '/veterinaria/gruposUsuarios/excluiGruposUsuarios', '/veterinaria/gruposUsuarios/retornaGruposUsuarios', '/veterinaria/gruposUsuarios/retornaDadosGrupoUsuarios'])) {
//         $response = new \Slim\Psr7\Response();
//         $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
//         return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//     }

//     return $handler->handle($request);
// });

// $app->group('/usuarios', function (RouteCollectorProxy $group) {

//     $group->post('/salvaUsuario',  App\Controllers\Usuarios::class . ':Salvar');

//     $group->post('/excluiUsuario',  App\Controllers\Usuarios::class . ':ExcluirUsuario');

//     $group->post('/retornaUsuarios',  App\Controllers\Usuarios::class . ':RetornarUsuarios');

//     $group->post('/retornaDadosUsuario',  App\Controllers\Usuarios::class . ':RetornarDadosUsuario');

//     $group->post('/ativaDesativaUsuario',  App\Controllers\Usuarios::class . ':AtivarDesativarUsuario');

// })->add(function (Request $request, RequestHandlerInterface $handler) {
//     $uri = $request->getUri()->getPath();
//     if (!in_array($uri, ['/veterinaria/usuarios/salvaUsuario', '/veterinaria/usuarios/excluiUsuario', '/veterinaria/usuarios/retornaUsuarios', '/veterinaria/usuarios/retornaDadosUsuario', '/veterinaria/usuarios/ativaDesativaUsuario'])) {
//         $response = new \Slim\Psr7\Response();
//         $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
//         return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//     }

//     return $handler->handle($request);
// });

$app->run();
