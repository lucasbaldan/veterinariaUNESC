<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

$config = json_decode(file_get_contents(__DIR__ . '/Configurations.json'), true);

foreach ($config as $key => $value) {
    $GLOBALS[$key] = $value;
}

$app = AppFactory::create();
$app->setBasePath('/veterinariaUNESC');
$app->addRoutingMiddleware();
$app->addErrorMiddleware($GLOBALS['desenvolvimento'], true, true);

$twig = Twig::create(__DIR__ . '/App/Views/paginas', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE TELAS

$app->get('/', function ($request, $response, array $args) {
    return $response->withHeader('Location', '/veterinariaUNESC/paginas/inicial')->withStatus(302);
});

$app->group('/paginas', function (RouteCollectorProxy $group) use ($twig) {

    $group->get('/login', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\LoginPage($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/fichaLPV', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\FormularioLPV($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/inicial', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\InicialPage($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/listTipoAnimal', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\listTipoAnimal($twig);
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
        $tela =  new App\Views\CadastroGruposUsuarios($twig);
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


})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, ['/veterinariaUNESC/paginas',
                         '/veterinariaUNESC/paginas/login', 
                         '/veterinariaUNESC/paginas/fichaLPV', 
                         '/veterinariaUNESC/paginas/inicial', 
                         '/veterinariaUNESC/paginas/listTipoAnimal',
                         '/veterinariaUNESC/paginas/listEspecie',
                         '/veterinariaUNESC/paginas/listRaca',
                         '/veterinariaUNESC/paginas/listMunicipio',
                         '/veterinariaUNESC/paginas/listBairro',
                         '/veterinariaUNESC/paginas/listLogradouro',
                         '/veterinariaUNESC/paginas/cadastroPessoas',
                         '/veterinariaUNESC/paginas/cadastroUsuarios',
                         '/veterinariaUNESC/paginas/cadastroAnimais',
                         '/veterinariaUNESC/paginas/gruposUsuarios',
                         '/veterinariaUNESC/paginas/listPessoas',
                         '/veterinariaUNESC/paginas/listAnimais',
                         '/veterinariaUNESC/paginas/listAtendimentos',
                         '/veterinariaUNESC/paginas/listUsuarios',
                         ])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});


/////////////////////// ROTAS DE REQUISIÇÕES PARA CARREGAMENTO DINÂMICO DE MODAIS

$app->group('/modais', function (RouteCollectorProxy $group) use ($twig) {

    $group->post('/cadastroTipoAnimal', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroTipoAnimalModal($twig);
        return $tela->exibir($request, $response, $args);
    });

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

})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, ['/veterinariaUNESC/modais/cadastroTipoAnimal',
                         '/veterinariaUNESC/modais/cadastroRaca',
                         '/veterinariaUNESC/modais/cadastroEspecie',
                         '/veterinariaUNESC/modais/cadastroMunicipio',
                         '/veterinariaUNESC/modais/cadastroLogradouro',
                         '/veterinariaUNESC/modais/cadastroBairro',
                         '/veterinariaUNESC/modais/buscaRapidaPessoa',])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE BACKEND

$app->group('/server', function (RouteCollectorProxy $group) {

    $group->group('/pessoas', function (RouteCollectorProxy $pessoasGroup) {
        $pessoasGroup->post('/controlar', App\Controllers\Pessoas::class . ':Salvar');
        $pessoasGroup->post('/retornaPesquisaModal', App\Controllers\Pessoas::class . ':retornaPesquisaModal');
        $pessoasGroup->post('/selecionarPessoa', App\Controllers\Pessoas::class . ':RetornarDadosPessoa');
        $pessoasGroup->post('/atualizaExclusaoPessoa', App\Controllers\Pessoas::class . ':AtualizarExclusaoPessoa');
        $pessoasGroup->post('/excluiPessoa', App\Controllers\Pessoas::class . ':ApagarPessoa');
        $pessoasGroup->post('/grid', App\Controllers\Pessoas::class . ':montarGrid');
        $pessoasGroup->post('/general', App\Controllers\Pessoas::class . ':General');

    });

    $group->group('/tipoAnimal', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\TiposAnimais::class . ':montarGrid');

        $Group->post('/general', App\Controllers\TiposAnimais::class . ':buscar');

        $Group->post('/controlar', App\Controllers\TiposAnimais::class . ':controlar');

        $Group->post('/excluir', App\Controllers\TiposAnimais::class . ':excluir');
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
    });

    $group->group('/atendimentos', function (RouteCollectorProxy $Group) {
        $Group->post('/grid', App\Controllers\Atendimentos::class . ':montarGrid');

        $Group->post('/controlar', App\Controllers\Atendimentos::class . ':controlar');

        $Group->post('/excluir', App\Controllers\Atendimentos::class . ':excluir');
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
    });

    $group->group('/usuarios', function (RouteCollectorProxy $usuariosGroup) {
        $usuariosGroup->post('/salvaUsuario',  App\Controllers\Usuarios::class . ':Salvar');
        $usuariosGroup->post('/excluiUsuario',  App\Controllers\Usuarios::class . ':ExcluirUsuario');
        $usuariosGroup->post('/retornaUsuarios',  App\Controllers\Usuarios::class . ':RetornarUsuarios');
        $usuariosGroup->post('/retornaDadosUsuario',  App\Controllers\Usuarios::class . ':RetornarDadosUsuario');
        $usuariosGroup->post('/ativaDesativaUsuario',  App\Controllers\Usuarios::class . ':AtivarDesativarUsuario');
        $usuariosGroup->post('/grid',  App\Controllers\Usuarios::class . ':montarGrid');
    });

    $group->group('/fichaLPV', function (RouteCollectorProxy $fichaLPVGroup) {
        $fichaLPVGroup->post('/salvafichaLPV',  App\Controllers\FormularioLPV::class . ':Salvar');
        $fichaLPVGroup->post('/retornaFichasLPV',  App\Controllers\FormularioLPV::class . ':RetornarFichasLPV');
        $fichaLPVGroup->post('/retornaDadosFichaLPV',  App\Controllers\FormularioLPV::class . ':RetornarDadosFichaLPV');
        $fichaLPVGroup->post('/apagaFichaLPV',  App\Controllers\FormularioLPV::class . ':ApagarFichaLPV');
    });

})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, [
        '/veterinariaUNESC/server/pessoas/controlar',
        '/veterinariaUNESC/server/pessoas/retornaPesquisaModal',
        '/veterinariaUNESC/server/pessoas/selecionarPessoa',
        '/veterinariaUNESC/server/pessoas/atualizaExclusaoPessoa',
        '/veterinariaUNESC/server/pessoas/excluiPessoa',
        '/veterinariaUNESC/server/pessoas/grid',
        '/veterinariaUNESC/server/pessoas/general',

        '/veterinariaUNESC/server/tipoAnimal/grid',
        '/veterinariaUNESC/server/tipoAnimal/controlar',
        '/veterinariaUNESC/server/tipoAnimal/excluir',
        '/veterinariaUNESC/server/tipoAnimal/general',

        '/veterinariaUNESC/server/especie/grid',
        '/veterinariaUNESC/server/especie/controlar',
        '/veterinariaUNESC/server/especie/excluir',
        '/veterinariaUNESC/server/especie/general',

        '/veterinariaUNESC/server/raca/grid',
        '/veterinariaUNESC/server/raca/controlar',
        '/veterinariaUNESC/server/raca/excluir',
        '/veterinariaUNESC/server/raca/general',

        '/veterinariaUNESC/server/municipio/grid',
        '/veterinariaUNESC/server/municipio/controlar',
        '/veterinariaUNESC/server/municipio/excluir',
        '/veterinariaUNESC/server/municipio/general',

        '/veterinariaUNESC/server/bairro/grid',
        '/veterinariaUNESC/server/bairro/controlar',
        '/veterinariaUNESC/server/bairro/excluir',
        '/veterinariaUNESC/server/bairro/general',

        '/veterinariaUNESC/server/logradouro/grid',
        '/veterinariaUNESC/server/logradouro/controlar',
        '/veterinariaUNESC/server/logradouro/excluir',
        '/veterinariaUNESC/server/logradouro/general',

        '/veterinariaUNESC/server/estado/general',

        '/veterinariaUNESC/server/animais/grid',
        '/veterinariaUNESC/server/animais/controlar',
        '/veterinariaUNESC/server/animais/excluir',

        '/veterinariaUNESC/server/atendimentos/grid',
        '/veterinariaUNESC/server/atendimentos/controlar',
        '/veterinariaUNESC/server/atendimentos/excluir',

        '/veterinariaUNESC/server/gruposUsuarios/salvaGrupoUsuarios',
        '/veterinariaUNESC/server/gruposUsuarios/excluiGruposUsuarios',
        '/veterinariaUNESC/server/gruposUsuarios/retornaGruposUsuarios',
        '/veterinariaUNESC/server/gruposUsuarios/retornaDadosGrupoUsuarios',
        '/veterinariaUNESC/server/gruposUsuarios/general',

        '/veterinariaUNESC/server/usuarios/salvaUsuario',
        '/veterinariaUNESC/server/usuarios/excluiUsuario',
        '/veterinariaUNESC/server/usuarios/retornaUsuarios',
        '/veterinariaUNESC/server/usuarios/retornaDadosUsuario',
        '/veterinariaUNESC/server/usuarios/ativaDesativaUsuario',
        '/veterinariaUNESC/server/usuarios/grid',

        '/veterinariaUNESC/server/fichaLPV/salvafichaLPV',
        '/veterinariaUNESC/server/fichaLPV/retornaFichasLPV',
        '/veterinariaUNESC/server/fichaLPV/retornaDadosFichaLPV',
        '/veterinariaUNESC/server/fichaLPV/apagaFichaLPV',
    ])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

// $app->group('/gruposUsuarios', function (RouteCollectorProxy $group) {

//     $group->post('/salvaGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':Salvar');

//     $group->post('/excluiGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':ExcluirGruposUsuarios');

//     $group->post('/retornaGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarGruposUsuarios');

//     $group->post('/retornaDadosGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarDadosGrupoUsuario');

// })->add(function (Request $request, RequestHandlerInterface $handler) {
//     $uri = $request->getUri()->getPath();
//     if (!in_array($uri, ['/veterinariaUNESC/gruposUsuarios/salvaGrupoUsuarios', '/veterinariaUNESC/gruposUsuarios/excluiGruposUsuarios', '/veterinariaUNESC/gruposUsuarios/retornaGruposUsuarios', '/veterinariaUNESC/gruposUsuarios/retornaDadosGrupoUsuarios'])) {
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
//     if (!in_array($uri, ['/veterinariaUNESC/usuarios/salvaUsuario', '/veterinariaUNESC/usuarios/excluiUsuario', '/veterinariaUNESC/usuarios/retornaUsuarios', '/veterinariaUNESC/usuarios/retornaDadosUsuario', '/veterinariaUNESC/usuarios/ativaDesativaUsuario'])) {
//         $response = new \Slim\Psr7\Response();
//         $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
//         return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//     }

//     return $handler->handle($request);
// });

$app->run();
