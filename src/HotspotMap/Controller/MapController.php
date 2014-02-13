<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MapController
{
    public function index(Request $request, Application $app)
    {
        $userMapper = $app['UserMapper'];

        $message = 'MapController index';

        return $message;
    }
}
