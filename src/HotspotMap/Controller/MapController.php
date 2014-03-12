<?php

namespace HotspotMap\Controller;

use Silex\Application;
use HotspotMap\Model\Place;
use Buzz\Browser;
use \Buzz\Client\Curl;
use \Geocoder\HttpAdapter\BuzzHttpAdapter;
use \Geocoder\Geocoder;
use Symfony\Component\HttpFoundation\Request;

class MapController extends HotspotMapController
{
    private $adapter;
    private $buzz;
    private $geocoder;

    public function __construct()
    {
        // Init GeoCoder
        $this->buzz = new Browser(new Curl());
        $this->adapter = new BuzzHttpAdapter($this->buzz);
        $this->geocoder = new Geocoder();
    }

    private function retrieveClientInfo()
    {
        $place = new Place();
        $clientInfo = json_decode(file_get_contents("http://ipinfo.io/"), true);
        list($latitude, $longitude) = explode(",", $clientInfo['loc']);

        $place->address = $clientInfo['city']." ".$clientInfo['region']." ".$clientInfo['country'];
        $place->longitude = $longitude;
        $place->latitude = $latitude;
        $place->country = $clientInfo['country'];
        $place->town = $clientInfo['city'];

        return $place;
    }

    public function userInfo(Application $app)
    {
        $request = $app['request'];
        $place = $this->retrieveClientInfo();

        $app['statusCode'] = 200;

        return $this->respond($app, $request, 'place', $place, 'places/show');
    }

    public function index(Application $app)
    {
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $place = $this->retrieveClientInfo();
        $places = $placeMapper->findValidated();

        $closestPlaces = $placeMapper->findClosestPlaces($place->latitude, $place->longitude, 15);

        $data = array(
            'closestPlaces' => $closestPlaces,
            'place' => $place,
            'places' => $places
        );

        $app['statusCode'] = 200;

        return $this->respond($app, $request, 'data', $data, 'map/index');
    }
}
