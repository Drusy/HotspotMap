<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MapController extends HotspotMapController
{
    public function index( Application $app)
    {
        $placeMapper = $app['PlaceMapper'];
        $userMapper = $app['UserMapper'];

        $places = $placeMapper->findAll();
        $users = $userMapper->findAll();

        $data = array(
            'users' => $users,
            'places' => $places
        );

        return $this->respond($app, 'data', $data, 'map/index');

        return $message;
    }
}
