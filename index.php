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


})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, ['/veterinariaUNESC/paginas/login', '/veterinariaUNESC/paginas/formularioLPV', '/veterinariaUNESC/paginas/inicial'])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE BACKEND

$app->group('/server', function (RouteCollectorProxy $group) {

    // Definição das rotas dentro do grupo /pessoas

    $group->post('/login',  App\Controllers\Pessoas::class . ':efetuarLogin');

    $group->post('/salvaFichaLPV',  App\Controllers\FormularioLPV::class . ':Salvar');

    $group->post('/retornaFichaLPV',  App\Controllers\FormularioLPV::class . ':RetornarFichasLPV');

    $group->post('/retornaDadosFichaLPV',  App\Controllers\FormularioLPV::class . ':RetornarDadosFichaLPV');
    
    $group->post('/apagaFichaLPV',  App\Controllers\FormularioLPV::class . ':ApagarFichaLPV');
    
    $group->post('/salvaGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':Salvar');
    
    $group->post('/excluiGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':ExcluirGruposUsuarios');
    
    $group->post('/retornaGruposUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarGruposUsuarios');
    
    $group->post('/retornaDadosGrupoUsuarios',  App\Controllers\GruposUsuarios::class . ':RetornarDadosGrupoUsuario');

})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, ['/veterinariaUNESC/server/login', '/veterinariaUNESC/server/salvaFichaLPV', '/veterinariaUNESC/server/retornaFichaLPV', '/veterinariaUNESC/server/retornaDadosFichaLPV', '/veterinariaUNESC/server/apagaFichaLPV', '/veterinariaUNESC/server/salvaGrupoUsuarios', '/veterinariaUNESC/server/excluiGruposUsuarios', '/veterinariaUNESC/server/retornaGruposUsuarios', '/veterinariaUNESC/server/retornaDadosGrupoUsuarios'])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

$app->run();
