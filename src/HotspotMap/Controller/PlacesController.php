<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class PlacesController extends HotspotMapController
{
    public function places(Application $app)
    {
        $placeMapper = $app['PlaceMapper'];
        $places = $placeMapper->findAll();

        return $this->respond($app, 'places', $places, 'places/list');
    }

    public function place(Application $app, $id)
    {
        $placeMapper = $app['PlaceMapper'];
        $place = $placeMapper->findById($id);

        if (null === $place) {
            return new Response('Place not found', 404);
        }

        return $this->respond($app, 'place', $place, 'places/show');
    }
}
