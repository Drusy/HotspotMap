<?php

require_once __DIR__.'/../vendor/autoload.php';
use \HotspotMap\Service\PlaceMapper;
use \HotspotMap\Service\UserMapper;

$app = new Silex\Application();

//configure app
$app['debug'] = true;
$app['dsn'] = 'mysql:host=localhost;dbname=HotspotMap';
$app['user'] = 'root';
$app['password'] = 'root';

//create service
$app['PlaceMapper'] = $app->share(function()use($app){
    return new PlaceMapper($app);
});
$app['UserMapper'] = $app->share(function()use($app){
    return new UserMapper($app);
});

$app->get('/', 'HotspotMap\\Controller\\MapController::index');

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
