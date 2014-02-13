<?php

namespace HotspotMap\Service;

class Mapper
{
    protected $con;

    public function __construct($app)
    {
        $dsn = $app['dsn'];
        $user = $app['user'];
        $password = $app['password'];

        $this->con = new Connection($dsn, $user, $password);
    }
}
