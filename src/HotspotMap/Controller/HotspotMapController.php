<?php

namespace HotspotMap\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HotspotMapController
{
    public function respond(Application $app, Request $request, $dataName, $data, $uri)
    {
        $negotiator = new \Negotiation\Negotiator();
        $priorities   = array('text/html', 'application/json', 'application/xml', '*/*');
        $contentType = $negotiator->getBest($request->headers->get('Accept'), $priorities);

        if (!isset($app['statusCode']) || empty($app['statusCode']))
            $app['statusCode'] = 200;

        if ($contentType != null) {
            switch ($contentType->getValue()) {
                case 'application/json':
                    return new Response(json_encode($data), $app['statusCode']);
                    break;
                case 'application/xml':
                    return $app['twig']->render($uri.'.xml.twig', array($dataName => $data));
                    break;
                default:
                    return $app['twig']->render($uri.'.html.twig', array($dataName => $data));
            }
        }
    }
}
