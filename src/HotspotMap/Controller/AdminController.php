<?php

namespace HotspotMap\Controller;

use Silex\Application;

class AdminController extends HotspotMapController
{

    public function __construct()
    {

    }

    public function managePlaces(Application $app)
    {
        $request = $app['request'];

        $placeMapper = $app['PlaceMapper'];
        $places = $placeMapper->findAllNonValidated();

        if ($request->getMethod() == 'POST') {
            foreach($places as $place)
            {
                $id = $place->getId();
                $status = $request->get($id);

                if($status == "create")
                {
                    $place = $placeMapper->findById($id);
                    if($place != null)
                    {
                        $place->validated = 1;
                        $placeMapper->save($place);
                    }
                }
                else if($status == "delete")
                {

                }
            }
        }

        return $app->redirect($app['url_generator']->generate('admin'));
    }

    public function index(Application $app)
    {
        $placeMapper = $app['PlaceMapper'];
        $places = $placeMapper->findAllNonValidated();

        return $this->respond($app, 'data', $places, 'admin/admin');
    }
}
