<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

// status

$app->get('/status', function ($request, $response) {
    $response->getBody()->write(json_encode(['status' => 'okk']));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

//GET
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$jogos = [
    ['id' => 1, 'nome' => 'Mario Kart'],
    ['id' => 2, 'nome' => 'The Legend of Zelda'],
    ['id' => 3, 'nome' => 'Crash Bandicoot 2'],
];

$app->get('/jogos/{id}', function ($request, $response, $args) use (&$jogos) {
     $id = (int)$args['id'];
     $jogo = array_filter($jogos, fn($item) => $item['id'] === $id);
     $jogo = reset($jogo);    
if (!$jogo) {
        $response->getBody()->write(json_encode(['erro' => 'Jogo não encontrado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
    $response->getBody()->write(json_encode($jogo));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

//POST
$app->post('/jogos', function ($request, $response) use (&$jogos) {
    $dados = $request->getParsedBody();
    $novoJogo = ['id' => count($jogos) + 1, 'nome' => $dados['nome']];
    $jogos[] = $novoJogo;
    $response->getBody()->write(json_encode($novoJogo));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});


$app->get('/jogos', function ($request, $response, $args) use (&$jogos) {
    $queryParams = $request->getQueryParams();
    $nome = $queryParams['nome '] ?: null;

    if ($nome) {
        $filtrados = array_filter($jogos, fn($item) => str_contains(mb_strtolower($item['nome']), mb_strtolower($nome)));
        $response->getBody()->write(json_encode($filtrados));
    } else {
        $response->getBody()->write(json_encode($jogos));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
});
$app->run();
