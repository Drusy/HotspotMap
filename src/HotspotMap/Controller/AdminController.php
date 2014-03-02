<?php

namespace HotspotMap\Controller;

use Silex\Application;

class AdminController extends HotspotMapController
{

    public function __construct()
    {

    }

    public function index(Application $app)
    {
        $data = ":D";

        return $this->respond($app, 'data', $data, 'admin/admin');
    }
}
