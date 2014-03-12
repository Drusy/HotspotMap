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
        $app['statusCode'] = 200;

        $placeMapper = $app['PlaceMapper'];
        $places = $request->request->all();

        if ($request->getMethod() == 'POST') {
            foreach ($places as $id => $status) {
                if ($status == "validate") {
                    $place = $placeMapper->findById($id);
                    if ($place != null) {
                        $place->validated = 1;
                        $placeMapper->save($place);
                    } else {
                        $app['statusCode'] = 400;
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
        $app['statusCode'] = 200;

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
                    } else {
                        $app['statusCode'] = 400;
                    }
                }
            }
        }

        return $app->redirect($app['url_generator']->generate('admin'));
    }

    public function updatePlaces(Application $app)
    {
        $request = $app['request'];
        $app['statusCode'] = 200;

        $placeMapper = $app['PlaceMapper'];
        $places = $request->request->all();

        if ($request->getMethod() == 'POST') {
            foreach ($places as $id => $status) {
                if ($status == "update") {
                    $placeUpdated = $placeMapper->findById($id);

                    $placeOrigin = $placeMapper->findById($placeUpdated->copy_of);
                    $placeMapper->deleteById($id);

                    if ($placeOrigin && $placeUpdated) {
                        $placeUpdated->copy_of = null;
                        $placeUpdated->validated = 1;
                        $placeMapper->save($placeOrigin->fillWith($placeUpdated));
                    } else {
                        $app['statusCode'] = 400;
                    }
                } elseif ($status == "delete") {
                    $placeMapper->deleteById($id);
                }
            }
        }

        return $app->redirect($app['url_generator']->generate('admin'));
    }

    public function index(Application $app)
    {
        $request = $app['request'];
        $placeMapper = $app['PlaceMapper'];
        $commentMapper = $app['CommentMapper'];

        $app['statusCode'] = 200;

        $data["nonvalidated"] = $placeMapper->findNonValidated();
        $data["validated"] = $placeMapper->findValidated();
        $data["comments"] = $commentMapper->findNonValidatedComment();
        $data["updated"] = $placeMapper->findUpdated();

        return $this->respond($app, $request, 'data', $data, 'admin/admin');
    }

    public function manageComment(Application $app)
    {
        $request = $app['request'];

        $commentMapper = $app['CommentMapper'];
        $places = $request->request->all();

        $app['statusCode'] = 200;

        if ($request->getMethod() == 'POST') {
            foreach ($places as $id => $status) {
                if ($status == "delete") {
                    $commentMapper->deleteById($id);
                } elseif ($status == "validate") {
                    $place = $commentMapper->findById($id);
                    if ($place != null) {
                        $place->validated = 1;
                        $commentMapper->save($place);
                    } else {
                        $app['statusCode'] = 400;
                    }
                }
            }
        }

        return $app->redirect($app['url_generator']->generate('admin'));
    }
}
