<?php

namespace HotspotMap\Controller;

use Silex\Application;
use HotspotMap\Model\Place;
use Buzz\Browser;
use \Buzz\Client\Curl;
use \Geocoder\HttpAdapter\BuzzHttpAdapter;
use \Geocoder\Geocoder;

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

    private function retrieveClientInfo() {
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
        $place = $this->retrieveClientInfo();

        $app['statusCode'] = 200;

        return $this->respond($app, 'place', $place, 'places/show');
    }

    public function index(Application $app)
    {
        $placeMapper = $app['PlaceMapper'];
        $place = $this->retrieveClientInfo();
        $places = $placeMapper->findAll();

        $closestPlaces = $placeMapper->findClosestPlaces($place->latitude, $place->longitude, 300);

        $data = array(
            'closestPlaces' => $closestPlaces,
            'place' => $place,
            'places' => $places
        );

        $app['statusCode'] = 200;

        return $this->respond($app, 'data', $data, 'map/index');
    }
}
