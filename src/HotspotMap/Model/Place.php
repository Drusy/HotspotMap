<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 11/02/14
 * Time: 16:02
 */

namespace HotspotMap\Model;

use HotspotMap\Model\ORM\PlaceModel;

class Place extends PlaceModel{

    function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }
} 