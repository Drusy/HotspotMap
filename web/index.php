<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->get('/', 'HotspotMap\\Controller\\MapController::index');

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name).'!';
});


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
    return $message;
});

$app->run();
