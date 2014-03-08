<?php

require_once __DIR__.'/../vendor/autoload.php';

use \HotspotMap\Service\PlaceMapper;
use \HotspotMap\Service\UserMapper;
use \HotspotMap\Service\CommentMapper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\UrlGeneratorServiceProvider;

$app = new Silex\Application();
$app['debug'] = true;

// Configure globals for database
$GLOBALS['dsn'] = 'mysql:host=localhost:3306;dbname=HotspotMap';
$GLOBALS['user'] = 'root';
$GLOBALS['password'] = 'root';

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
$app['CommentMapper'] = $app->share(function () use ($app) {
    return new CommentMapper($app);
});

// Authentification info
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/login$',
        'anonymous' => true,
    ),
    'secured' => array(
        'anonymous' => true,
        'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
        'logout' => array('logout_path' => '/logout'),
        'users' => $app->share(function () use ($app) {
            return $app['UserMapper'];
        }),
    )
);

$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN'),
);

/*
$app['security.encoder.digest'] = $app->share(function ($app) {
    // use the sha512 algorithm
    // don't base64 encode the password
    // use 6 iterations
    return new MessageDigestPasswordEncoder('sha512', false, 6);
});
*/

$app->register(new UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => $app['security.firewalls'],
    'security.access_rules' => $app['security.access_rules']
));

// Manage controllers
$app->get('/', 'HotspotMap\\Controller\\MapController::index')
    ->bind('root');
$app->get('/userInfo', 'HotspotMap\\Controller\\MapController::userInfo');

$app->post('/places', 'HotspotMap\\Controller\\PlacesController::addPlace');
$app->post('/places/find', 'HotspotMap\\Controller\\PlacesController::findPlace');
$app->get('/places', 'HotspotMap\\Controller\\PlacesController::places');
$app->get('/places/{id}', 'HotspotMap\\Controller\\PlacesController::placeFromId');
$app->put('/places/{id}', 'HotspotMap\\Controller\\PlacesController::updatePlace');

$app->post('/place/{id}/comment', 'HotspotMap\\Controller\\PlacesController::addCommentForId');

$app->get('/users', 'HotspotMap\\Controller\\UsersController::users');
$app->get('/users/{id}', 'HotspotMap\\Controller\\UsersController::user');

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('users/login.html.twig', array(
        'error'         => $app['security.last_error']($request)
    ));
});

$app->get('/about', function (Request $request) use ($app) {
    return $app['twig']->render('about/about.html.twig');
})
    ->bind('about');

$app->get('/contact', function (Request $request) use ($app) {
    return $app['twig']->render('contact/contact.html.twig');
})
    ->bind('contact');

$app->get('/admin', 'HotspotMap\\Controller\\AdminController::index')
    ->bind('admin');

$app->post('/admin/save', 'HotspotMap\\Controller\\AdminController::managePlaces');

$app->post('/admin/remove', 'HotspotMap\\Controller\\AdminController::removePlaces');

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

    if (isset($app['statusCode']) && !empty($app['statusCode'])) {
        $response->setStatusCode($app['statusCode']);
        $app['statusCode'] = '';
    }
    $response->headers->set('Content-Type', $contentType);
});

$app->run();
