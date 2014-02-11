<?php

require_once __DIR__.'/../vendor/autoload.php';
use \HotspotMap\Service\PlaceMapper;
use \HotspotMap\Service\UserMapper;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$negotiator = new \Negotiation\Negotiator();

//configure app
$app['debug'] = true;
$app['dsn'] = 'mysql:host=localhost;dbname=HotspotMap';
$app['user'] = 'root';
$app['password'] = 'root';

// Register Services
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'../Resources/views',
));
$app['PlaceMapper'] = $app->share(function()use($app){
    return new PlaceMapper($app);
});
$app['UserMapper'] = $app->share(function()use($app){
    return new UserMapper($app);
});

$app->get('/', 'HotspotMap\\Controller\\MapController::index');

// Error management
$app->error(function (\Exception $e, $code) use($app, $negotiator) {
    if($app['debug']) {
        return;
    }

    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return new Response($message);
});

$app->run();
