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

        error_log($lat . " " . $lng);

        $placeMapper = $app['PlaceMapper'];
        $place = $placeMapper->findByLatLng($lat, $lng);

        if (null === $place) {
            $app['statusCode'] = 404;
            return new Response('Place not found', $app['statusCode']);
        }
        $app['statusCode'] = 200;

        return $this->respond($app, 'place', $place, 'places/show');
    }

    public function addPlace(Application $app)
    {
        $request = $app['request'];
        $error = array( "status" => "error",
                        "message" => "Cannot insert the place.");
        $placeMapper = $app['PlaceMapper'];
        $place = new Place();

        $place->name = $request->get('name');
        $place->address = $request->get('address');
        $place->country = $request->get('country');
        $place->town = $request->get('town');
        $place->setWebsite($request->get('website'));
        $place->latitude = $request->get('latitude');
        $place->longitude = $request->get('longitude');
        $place->description = $request->get('description');

        if (empty($place->latitude) || empty($place->longitude)) {
            $geocoded = $this->geocoder->geocode($place->address . " " . $place->town . " " . $place->country);
            $place->latitude = $geocoded['latitude'];
            $place->longitude = $geocoded['longitude'];
        }

        if ($placeMapper->save($place)) {
            $app['statusCode'] = 201;
            return $this->respond($app, 'place', $place, 'places/show');
        } else {
            $app['statusCode'] = 400;
            return $this->respond($app, 'response', $error, 'places/response');
        }
    }
}
