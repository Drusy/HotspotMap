<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MapController {

    public function index(Request $request, Application $app) {
        $userMapper = $app['UserMapper'];

        $message = 'MapController index';

        return $message;
    }
}