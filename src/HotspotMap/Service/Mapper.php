<?php

namespace HotspotMap\Service;

class Mapper
{
    protected $con;

    public function __construct()
    {
        $this->con = Connection::getConnection();
    }
}
