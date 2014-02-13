<?php

namespace HotspotMap\Model;

use HotspotMap\Model\ORM\PlaceModel;

class Place extends PlaceModel
{
    public function __construct($id = null)
    {
        if ($id == null) {
            $this->id = uniqid();
            $this->pseudo = uniqid("place_");
        } else {
            $this->id = $id;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setWebsite($website)
    {
        // TODO : VERIF
        $this->website = $website;
    }

    public function getWebsite()
    {
        return $this->website;
    }
}
