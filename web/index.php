<?php

require_once __DIR__.'/../vendor/autoload.php';

use \HotspotMap\Service\PlaceMapper;
use \HotspotMap\Service\UserMapper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

// Configure app
$app['debug'] = true;
$app['dsn'] = 'mysql:host=localhost:3306;dbname=HotspotMap';
$app['user'] = 'root';
$app['password'] = 'root';

// Register Services
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../app/Resources/views'
));
$app['PlaceMapper'] = $app->share(function () use ($app) {
    return new PlaceMapper($app);
});
$app['UserMapper'] = $app->share(function () use ($app) {
    return new UserMapper($app);
});

// Manage controllers
$app->get('/', 'HotspotMap\\Controller\\MapController::index');
$app->get('/userInfo', 'HotspotMap\\Controller\\MapController::userInfo');

$app->post('/places', 'HotspotMap\\Controller\\PlacesController::addPlace');
$app->get('/places', 'HotspotMap\\Controller\\PlacesController::places');
$app->get('/places/{id}', 'HotspotMap\\Controller\\PlacesController::place');

$app->get('/users', 'HotspotMap\\Controller\\UsersController::users');
$app->get('/users/{id}', 'HotspotMap\\Controller\\UsersController::user');

// Error management
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    switch ($code) {
        case 400:
            $message = 'Bad request.';
            break;
        case 404:
            $message = 'Page not found.';
            break;
        default:
            $message = 'Internal Server Error.';
    }

    return new Response($message, $code);
});

// REST response
$app->after(function (Request $request, Response $response) use ($app) {
    $negotiator = new \Negotiation\Negotiator();
    $contentType = $negotiator->getBest($_SERVER['HTTP_ACCEPT'])->getValue();

    if ($contentType == '*/*') {
        $contentType = 'text/html';
    }

    $response->setStatusCode($app['statusCode']);
    $response->headers->set('Content-Type', $contentType);
});

$app->run();
