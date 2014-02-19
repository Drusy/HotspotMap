<?php

namespace HotspotMap\Controller;

use Silex\Application;

class MapController extends HotspotMapController
{
    private $geocoder;
    private $adapter;
    private $buzz;
    private $geolocalize;

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

        if ($clientIp === '::1' || $clientIp === '127.0.0.1') {
            $this->geolocalize = 'Clermont-Ferrand';
            $this->geocoder->registerProviders(array(
                new \Geocoder\Provider\GoogleMapsProvider($this->adapter, 'fr_FR', 'France', true)
            ));

        } else {
            $this->geolocalize = $clientIp;
            $this->geocoder->registerProviders(array(
                new \Geocoder\Provider\FreeGeoIpProvider($this->adapter)
            ));
        }
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
        $data['geocoder'] = $this->geocoder->geocode($this->geolocalize);

        return $this->respond($app, 'data', $data, 'map/index');
    }
}
