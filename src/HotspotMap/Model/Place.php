<?php

namespace HotspotMap\Model;

use HotspotMap\Model\ORM\PlaceModel;

class Place extends PlaceModel implements \JsonSerializable
{
    public function __construct($id = null)
    {
        if ($id == null) {
            $this->id = uniqid();
        } else {
            $this->id = $id;
        }

        $this->distance = 0;
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

    public function jsonSerialize()
    {
        $json = array();
        foreach ($this as $key => $value) {
            $json[$key] = $value;
        }

        return $json;
    }
}
