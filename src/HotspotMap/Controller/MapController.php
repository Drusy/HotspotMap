<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MapController {

    public function index(Request $request, Application $app){
        $um = $app['UserMapper'];

        $mess = 'et ca marche bien';
        return $mess;
    }
}
