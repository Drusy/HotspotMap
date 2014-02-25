<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use HotspotMap\Model\Place;

class PlacesController extends HotspotMapController
{
    private $adapter;
    private $buzz;
    private $geocoder;

    public function __construct()
    {
        // Init GeoCoder
        $this->buzz = new \Buzz\Browser(new \Buzz\Client\Curl());
        $this->adapter = new \Geocoder\HttpAdapter\BuzzHttpAdapter($this->buzz);
        $this->geocoder = new \Geocoder\Geocoder();


        $provider = new \Geocoder\Provider\GoogleMapsProvider($this->adapter, 'fr_FR', 'France', true);
        $this->geocoder->registerProvider($provider);
    }

    public function places(Application $app)
    {
        $placeMapper = $app['PlaceMapper'];
        $places = $placeMapper->findAll();

        $app['statusCode'] = 200;

        return $this->respond($app, 'places', $places, 'places/list');
    }

    public function placeFromId(Application $app, $id)
    {
        $placeMapper = $app['PlaceMapper'];
        $place = $placeMapper->findById($id);

        if (null === $place) {
            $app['statusCode'] = 404;
            return new Response('Place not found', $app['statusCode']);
        }
        $app['statusCode'] = 200;

        return $this->respond($app, 'place', $place, 'places/show');
    }

    public function placeFromLatLng(Application $app, $lat, $lng)
    {
        // TODO : Change route method ...
        $lat = str_replace("_", ".", $lat);
        $lng = str_replace("_", ".", $lng);

        $placeMapper = $app['PlaceMapper'];
        $place = $placeMapper->findByLatLng($lat, $lng);

        if (null === $place) {
            $app['statusCode'] = 404;
            return new Response('Place not found', $app['statusCode']);
        }
        $app['statusCode'] = 200;

        return $this->respond($app, 'place', $place, 'places/show');
    }

    public function updatePlace(Application $app, $id)
    {
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $place = $this->fillPlaceFromRequest($request, $id);

        if (empty($place->latitude) || empty($place->longitude)) {
            $place = $this->geocodeFromAddress($place);
        }

        if ($placeMapper->save($place)) {
            $app['statusCode'] = 200;
            return $this->respond($app, 'place', $place, 'places/show');
        }

        $app['statusCode'] = 400;
        return new Response('Cannot update', $app['statusCode']);
    }

    public function addPlace(Application $app)
    {
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $place = $this->fillPlaceFromRequest($request);

        if (empty($place->latitude) || empty($place->longitude)) {
            $place = $this->geocodeFromAddress($place);
        } else if (!empty($place->latitude) && !empty($place->longitude)) {
            $place = $this->geocodeFromLatLng($place);
        }

        if ($placeMapper->save($place)) {
            $app['statusCode'] = 201;
            return $this->respond($app, 'place', $place, 'places/show');
        }

        $app['statusCode'] = 400;
        return new Response('Cannot insert', $app['statusCode']);
    }

    private function fillPlaceFromRequest($request, $id = null)
    {
        $place = new Place($id);

        $place->name = $request->get('name');
        $place->address = $request->get('address');
        $place->country = $request->get('country');
        $place->town = $request->get('town');
        $place->setWebsite($request->get('website'));
        $place->latitude = $request->get('latitude');
        $place->longitude = $request->get('longitude');
        $place->description = $request->get('description');

        return $place;
    }

    private function geocodeFromLatLng($place)
    {
        $geocoded = $this->geocoder->reverse($place->latitude, $place->longitude);

        $place->address = $geocoded['streetNumber']." ".$geocoded['streetName'];
        $place->country = $geocoded['country'];
        $place->town = $geocoded['city'];

        return $place;
    }

    private function geocodeFromAddress($place)
    {
        $geocoded = $this->geocoder->geocode($place->address . " " . $place->town . " " . $place->country);
        $place->latitude = $geocoded['latitude'];
        $place->longitude = $geocoded['longitude'];

        return $place;
    }
}
