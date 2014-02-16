<?php

namespace HotspotMap\Service;

class Mapper
{
    protected $con;

    public function __construct($app)
    {
        $this->con = Connection::getConnection($app);
    }
}
