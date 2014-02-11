<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$negotiator = new \Negotiation\Negotiator();

$app['debug'] = true;

// Register Services
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'../Resources/views',
));

// Index
$app->get('/', 'HotspotMap\\Controller\\MapController::index');

// Controllers
$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name).'!';
})
->value('name', 'unknown');

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
