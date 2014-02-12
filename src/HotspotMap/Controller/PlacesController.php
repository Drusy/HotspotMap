<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class PlacesController extends HotspotMapController  {

    public function places(Application $app) {
        // TODO : Get places from database
        $places = array(
            array('name' => 'Aberdeen'),
            array('name' => 'Clermont-Ferrand')
        );

        return $this->respond($app, 'places', $places, 'places/list');
    }

    public function place(Application $app, $id) {
        // TODO : Get place from database
        $place = array(
            'name' => 'Aberdeen',
            'id' => $id
        );

        if (null === $place) {
            return new Reponse('Place not found', 404);
        }

        return $this->respond($app, 'place', $place, 'places/show');
    }
}