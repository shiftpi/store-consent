<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Shiftpi\StoreConsent\Database;
use Shiftpi\StoreConsent\Validate;
use Slim\Factory\AppFactory;

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);

$config = require __DIR__ . '/../config/settings.php';

$app->add(function(Request $request, RequestHandler $handler) {
    $response = $handler->handle($request);
    return $response->withAddedHeader('Content-Type', 'application/json');
});

$app
    ->post('/consent', function(Request $request, Response $response) {
        $response->getBody()->write(json_encode(['id' => $request->getAttribute('route')->getArgument('visitor_id')]));
        return $response;
    })
    ->add(new Database($config['db_dsn']))
    ->add(new Validate($config['consent_categories']))
    ->add(function(Request $request, RequestHandler $handler) {
        $id = hash('sha3-256', random_bytes(100));
        $request->getAttribute('route')->setArgument('visitor_id', $id);

        return $handler->handle($request);
    });

$app
    ->put('/consent/{visitor_id}', function(Request $request, Response $response) {
        return $response;
    })
    ->add(new Database($config['db_dsn']))
    ->add(new Validate($config['consent_categories']))
    ->add(function(Request $request, RequestHandler $handler) {
        parse_str($request->getBody()->getContents(), $body);
        return $handler->handle($request->withParsedBody($body));
    });


$app->run();