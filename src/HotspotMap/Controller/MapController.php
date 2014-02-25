<?php

namespace HotspotMap\Controller;

use Silex\Application;
use HotspotMap\Model\Place;

class MapController extends HotspotMapController
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
        $userMapper = $app['UserMapper'];
        $place = $this->retrieveClientInfo();

        $places = $placeMapper->findAll();
        $users = $userMapper->findAll();
        $closestPlaces = $placeMapper->findClosestPlaces($place->latitude, $place->longitude, 300);

        $data = array(
            'users' => $users,
            'places' => $places,
            'closestPlaces' => $closestPlaces,
            'place' => $place
        );

        $app['statusCode'] = 200;

        return $this->respond($app, 'data', $data, 'map/index');
    }
}
