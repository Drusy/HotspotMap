<?php

namespace HotspotMap\Model\ORM;

class PlaceModel
{
    public $latitude;
    public $longitude;
    public $address;
    public $country;
    public $town;
    public $name;
    public $distance;
    public $description;
    public $validated;

    protected $id;
    protected $website;
}
