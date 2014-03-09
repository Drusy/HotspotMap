<?php

namespace HotspotMap\Model;

use HotspotMap\Model\ORM\PlaceModel;

class Place extends PlaceModel implements \JsonSerializable
{
    //lazy loading
    public $comments;

    public function __construct($id = null)
    {
        if ($id == null) {
            $this->id = uniqid();
        } else {
            $this->id = $id;
        }

        $this->distance = 0;
        $this->validated = 0;
        $this->copy_of = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setWebsite($website)
    {
        // TODO : Check website syntax
        $this->website = $website;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function fillWith(Place $placeOrigin)
    {
        $this->copy_of = $placeOrigin->copy_of;
        $this->validated = $placeOrigin->validated;

        $this->latitude = $placeOrigin->latitude;
        $this->longitude = $placeOrigin->longitude;
        $this->address = $placeOrigin->address;
        $this->country = $placeOrigin->country;
        $this->town = $placeOrigin->town;
        $this->name = $placeOrigin->name;
        $this->distance = $placeOrigin->distance;
        $this->description = $placeOrigin->description;



        $this->setWebsite($placeOrigin->getWebsite());

        return $this;
    }

    public function jsonSerialize()
    {
        $json = array();
        foreach ($this as $key => $value) {
            $json[$key] = $value;
        }

        return $json;
    }
}
