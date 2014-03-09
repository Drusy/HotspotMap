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
        $places = $request->request->all();

        if ($request->getMethod() == 'POST') {
            foreach ($places as $id => $status) {
                if ($status == "validate") {
                    $place = $placeMapper->findById($id);
                    if ($place != null) {
                        $place->validated = 1;
                        $placeMapper->save($place);
                    }
                } elseif ($status == "delete") {
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
        $places = $request->request->all();

        if ($request->getMethod() == 'POST') {
            foreach ($places as $id => $status) {
                if ($status == "delete") {
                    $placeMapper->deleteById($id);
                } elseif ($status == "unvalidate") {
                    $place = $placeMapper->findById($id);
                    if ($place != null) {
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
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $data["nonvalidated"] = $placeMapper->findAllNonValidated();
        $data["validated"] = $placeMapper->findAllValidated();

        return $this->respond($app, $request, 'data', $data, 'admin/admin');
    }
}
