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

$app->group('/paginas', function (RouteCollectorProxy $group) use ($twig) {

    $group->get('/login', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\LoginPage($twig);
        return $tela->exibir($request, $response, $args);
    });

    $group->get('/formularioLPV', function (Request $request, Response $response, $args) use ($twig) {
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

    $group->get('/cadastroPessoas', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\CadastroPessoas($twig);
        return $tela->exibir($request, $response, $args);
    });
})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, [
        '/veterinariaUNESC/paginas/login',
        '/veterinariaUNESC/paginas/formularioLPV',
        '/veterinariaUNESC/paginas/inicial',
        '/veterinariaUNESC/paginas/listTipoAnimal',
        '/veterinariaUNESC/paginas/cadastroPessoas'
    ])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE BACKEND

$app->group('/server', function (RouteCollectorProxy $group) {

    $group->group('/pessoas', function (RouteCollectorProxy $pessoasGroup) {
        $pessoasGroup->post('/login', App\Controllers\Pessoas::class . ':efetuarLogin');
    });

    $group->group('/tipoAnimal', function (RouteCollectorProxy $pessoasGroup) {
        $pessoasGroup->post('/grid', App\Controllers\TiposAnimais::class . ':montarGrid');
    });

    $group->group('/gruposUsuarios', function (RouteCollectorProxy $GrUsuariosGroup) {
        $GrUsuariosGroup->post('/salvaGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':Salvar');
        $GrUsuariosGroup->post('/excluiGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':ExcluirGruposUsuarios');
        $GrUsuariosGroup->post('/retornaGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarGruposUsuarios');
        $GrUsuariosGroup->post('/retornaDadosGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarDadosGrupoUsuario');
    });

    $group->group('/usuarios', function (RouteCollectorProxy $usuariosGroup) {
        $usuariosGroup->post('/salvaUsuario',  App\Controllers\Usuarios::class . ':Salvar');
        $usuariosGroup->post('/excluiUsuario',  App\Controllers\Usuarios::class . ':ExcluirUsuario');
        $usuariosGroup->post('/retornaUsuarios',  App\Controllers\Usuarios::class . ':RetornarUsuarios');
        $usuariosGroup->post('/retornaDadosUsuario',  App\Controllers\Usuarios::class . ':RetornarDadosUsuario');
        $usuariosGroup->post('/ativaDesativaUsuario',  App\Controllers\Usuarios::class . ':AtivarDesativarUsuario');
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
        '/veterinariaUNESC/server/pessoas/login',
        '/veterinariaUNESC/server/tipoAnimal/grid',

        '/veterinariaUNESC/server/gruposUsuarios/salvaGrupoUsuarios',
        '/veterinariaUNESC/server/gruposUsuarios/excluiGruposUsuarios',
        '/veterinariaUNESC/server/gruposUsuarios/retornaGruposUsuarios',
        '/veterinariaUNESC/server/gruposUsuarios/retornaDadosGrupoUsuarios',

        '/veterinariaUNESC/server/usuarios/salvaUsuario',
        '/veterinariaUNESC/server/usuarios/excluiUsuario',
        '/veterinariaUNESC/server/usuarios/retornaUsuarios',
        '/veterinariaUNESC/server/usuarios/retornaDadosUsuario',
        '/veterinariaUNESC/server/usuarios/ativaDesativaUsuario',

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
