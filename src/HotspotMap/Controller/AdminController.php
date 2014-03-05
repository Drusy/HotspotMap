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
                    $placeMapper->deleteById($id);
                }
            }
        }

        return $app->redirect($app['url_generator']->generate('admin'));
    }

    public function removePlaces(Application $app)
    {
        $request = $app['request'];

        $placeMapper = $app['PlaceMapper'];
        $places = $placeMapper->findAllValidated();

        if ($request->getMethod() == 'POST') {
            foreach($places as $place)
            {
                $id = $place->getId();
                $status = $request->get($id);

                if($status == "delete")
                {
                    $placeMapper->deleteById($id);
                }
                else if($status == "unvalidate")
                {
                    $place = $placeMapper->findById($id);
                    if($place != null)
                    {
                        $place->validated = 0;
                        $placeMapper->save($place);
                    }
                }
            }
        }

        return $app->redirect($app['url_generator']->generate('admin'));
    }

    public function index(Application $app)
    {
        $placeMapper = $app['PlaceMapper'];
        $data["nonvalidated"] = $placeMapper->findAllNonValidated();
        $data["validated"] = $placeMapper->findAllValidated();

        return $this->respond($app, 'data', $data, 'admin/admin');
    }
}
