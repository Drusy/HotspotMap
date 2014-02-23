<?php

namespace HotspotMap\Controller;

use Silex\Application;

class MapController extends HotspotMapController
{
    private $adapter;
    private $buzz;

    private function retrieveClientInfo() {
        return json_decode(file_get_contents("http://ipinfo.io/"), true);
    }

    public function __construct()
    {
        // Init GeoCoder
        $this->buzz = new \Buzz\Browser(new \Buzz\Client\Curl());
        $this->adapter = new \Geocoder\HttpAdapter\BuzzHttpAdapter($this->buzz);
        $this->geocoder = new \Geocoder\Geocoder();

        // Manage client Ip Address
        $clientIp = $_SERVER['REMOTE_ADDR'];
    }

    public function index(Application $app)
    {
        $placeMapper = $app['PlaceMapper'];
        $userMapper = $app['UserMapper'];
        $clientInfo = $this->retrieveClientInfo();
        list($latitude, $longitude) = explode(",", $clientInfo['loc']);

        $places = $placeMapper->findAll();
        $users = $userMapper->findAll();
        $closestPlaces = $placeMapper->findClosestPlaces($latitude, $longitude, 10);

        $data = array(
            'users' => $users,
            'places' => $places,
            'closestPlaces' => $closestPlaces,
            'clientInfo' => $clientInfo
        );

        return $this->respond($app, 'data', $data, 'map/index');
    }
}
