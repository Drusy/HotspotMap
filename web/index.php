<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/hello', function() {
    return 'Hello!';
});

$app->get('/kevin', function() {
    return '<h1>Kevin is a noob!</h1>';
});

$app->run();
