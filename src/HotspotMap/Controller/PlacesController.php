<?php

namespace HotspotMap\Controller;

use HotspotMap\Model\Comment;
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
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $places = $placeMapper->findValidated();

        $app['statusCode'] = 200;

        return $this->respond($app, $request, 'places', $places, 'places/list');
    }

    public function getCommentsForId(Application $app, $id)
    {
        $request = $app['request'];
        $commentMapper = $app['CommentMapper'];
        $placeMapper = $app['PlaceMapper'];
        $place = $placeMapper->findById($id);
        $app['statusCode'] = 200;

        if (null === $place) {
            $app['statusCode'] = 404;

            return new Response('Place not found', $app['statusCode']);
        }

        $comments = $commentMapper->findValidatedByPlaceId($id);

        return $this->respond($app, $request,'comments', $comments, 'comments/list');
    }

    public function placeFromId(Application $app, $id)
    {
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $place = $placeMapper->findById($id);

        if (null === $place) {
            $app['statusCode'] = 404;

            return new Response('Place not found', $app['statusCode']);
        }

        $app['statusCode'] = 200;

        return $this->respond($app, $request, 'place', $place, 'places/show');
    }

    public function updatePlace(Application $app, $id)
    {
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $place = $this->fillPlaceFromRequest($request);
        $place->copy_of = $id;
        $message = $this->validatePlace($place);

        $app['statusCode'] = 400;

        if ($place->valid) {
            if (empty($place->latitude) || empty($place->longitude)) {
                $place = $this->geocodeFromAddress($place);
            } else {
                $place = $this->geocodeFromAddress($place);
            }

            if ($placeMapper->save($place)) {
                $app['statusCode'] = 200;

                return $this->respond($app, $request, 'place', $place, 'places/show');
            }

            return new Response('Cannot insert', $app['statusCode']);

        }

        return new Response($message, $app['statusCode']);
    }

    public function addPlace(Application $app)
    {
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $place = $this->fillPlaceFromRequest($request);
        $message = $this->validatePlace($place);

        $app['statusCode'] = 400;

        if ($place->valid) {
            if (!empty($place->latitude) && !empty($place->longitude)) {
                $place = $this->geocodeFromLatLng($place);
            } else {
                $place = $this->geocodeFromAddress($place);
            }

            if ($placeMapper->save($place)) {
                $app['statusCode'] = 201;

                return $this->respond($app, $request, 'place', $place, 'places/show');
            }

            return new Response('Cannot insert', $app['statusCode']);
        }

        return new Response($message, $app['statusCode']);
    }

    public function findPlace(Application $app)
    {
        $request = $app['request'];
        $address = $request->get("address");
        $latitude = $request->get("latitude");
        $longitude = $request->get("longitude");

        if ($address == "" && $latitude == "" && $longitude == "") {
            $app['statusCode'] = 400;

            return new Response("Empty search ...", $app['statusCode']);
        }

        if (!empty($latitude) && !empty($longitude)) {
            $geocoded = $this->geocoder->reverse($latitude, $longitude);
        } else {
            $geocoded = $this->geocoder->geocode($address);
        }

        $place = new Place();
        $place->address = $geocoded['streetNumber']." ".$geocoded['streetName']." ".$geocoded['city']." ".$geocoded['country'];
        $place->country = $geocoded['country'];
        $place->town = $geocoded['city'];
        $place->latitude = $geocoded['latitude'];
        $place->longitude = $geocoded['longitude'];

        $app['statusCode'] = 200;

        return $this->respond($app, $request, 'place', $place, 'places/show');
    }

    private function validateComment($comment)
    {
        $noName = "You must provide a name";
        $noContent = "You must provide a content";
        $success = "success";

        $comment->valid = false;

        if ($comment->author == "") {
            return $noName;
        } elseif ($comment->content == "") {
            return $noContent;
        }

        $comment->valid = true;

        return $success;
    }

    private function validatePlace($place)
    {
        $noAddressLatLnError = "You must provide an address or coordinates of your place.";
        $noPlaceName = "You must provide a name to your place";
        $success = "success";

        $place->valid = false;

        if ($place->address == "" &&  $place->latitude == "" && $place->longitude == "") {
            return $noAddressLatLnError;
        } elseif ($place->name == "") {
            return $noPlaceName;
        }

        $place->valid = true;

        return $success;
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

    private function fillCommentFromRequest($request, $id = null)
    {
        $comment = new Comment();
        $comment->author = $request->get('author');
        $comment->content = $request->get('content');
        $comment->avatar = $request->get('avatar');
        $comment->place = $id;
        $comment->validated = 0;
        error_log($comment->content);

        return $comment;
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

    public function addCommentForId(Application $app, $id)
    {
        $placeMapper = $app['PlaceMapper'];
        $commentMapper = $app['CommentMapper'];
        $request = $app['request'];
        $place = $placeMapper->findById($id);

        $app['statusCode'] = 400;

        if (null === $place) {
            return new Response('Place not found', $app['statusCode']);
        }

        $comment = $this->fillCommentFromRequest($request, $id);
        $message = $this->validateComment($comment);

        if ($comment->valid) {
            if ($commentMapper->save($comment)) {
                $app['statusCode'] = 201;

                return new Response('Comment inserted', $app['statusCode']);
            } else {
                return new Response('Cannot insert', $app['statusCode']);
            }
        }

        return new Response($message, $app['statusCode']);
    }
}
