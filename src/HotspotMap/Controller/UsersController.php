<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends HotspotMapController  {

    public function users(Application $app) {
        // TODO : Get users from database
        $users = array(
            array('name' => 'Florian'),
            array('name' => 'Kevin')
        );

        return $this->respond($app, 'users', $users, 'users/list');
    }

    public function user(Application $app, $id) {
        // TODO : Get user from database
        $user = array(
            'name' => 'Florian',
            'id' => $id
        );

        if (null === $user) {
            return new Reponse('Place not found', 404);
        }

        return $this->respond($app, 'user', $user, 'users/show');
    }
}