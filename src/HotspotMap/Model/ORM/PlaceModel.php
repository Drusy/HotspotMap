<?php

namespace HotspotMap\Model\ORM;

class PlaceModel
{
    public $latitude = 0;
    public $longitude = 0;
    public $address = "";
    public $country = "";
    public $town = "";
    public $name = "";
    public $distance = 0;
    public $description = "";
    public $validated = 0;
    public $copy_of = 0;

    protected $website = "";
    protected $id;
}
