<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class HotspotMapController {

    public function respond(Application $app, $dataName, $data, $uri) {
        $negotiator = new \Negotiation\Negotiator();
        $contentType = $negotiator->getBest($_SERVER['HTTP_ACCEPT']);

        switch ($contentType->getValue()) {
            case 'application/json':
                return new Response(json_encode($data), 200);
                break;
            case 'application/xml':
                return $app['twig']->render($uri.'.xml.twig', array($dataName => $data));
                break;
            default:
                return $app['twig']->render($uri.'.html.twig', array($dataName => $data));
        }
    }
}