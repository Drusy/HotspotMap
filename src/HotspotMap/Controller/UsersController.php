<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends HotspotMapController
{
    public function users(Application $app)
    {
        $request = $app['request'];
        $userMapper = $app['UserMapper'];
        $users = $userMapper->findAll();
        $app['statusCode'] = 200;

        return $this->respond($app, $request, 'users', $users, 'users/list');
    }

    public function user(Application $app, $id)
    {
        $request = $app['request'];
        $userMapper = $app['UserMapper'];
        $user = $userMapper->findById($id);

        if (null === $user) {
            $app['statusCode'] = 404;

            return new Response('User not found', $app['statusCode']);
        }

        return $this->respond($app, $request, 'user', $user, 'users/show');
    }
}
