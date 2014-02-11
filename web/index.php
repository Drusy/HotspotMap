<?php

require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['debug'] = true;

// Index
$app->get('/', 'HotspotMap\\Controller\\MapController::index');

// Controllers
$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name).'!';
})
->value('name', 'unknown');

// Error management
$app->error(function(\Exception $e, $code) use($app) {
    if($app['debug'])
        return;

    switch ($code)
    {
        case 404 :
            $message='404 page to overload';
            break;
        default:
            $message='Error not 404 to overload';
    }
    return new Response($message);
});

$app->run();
