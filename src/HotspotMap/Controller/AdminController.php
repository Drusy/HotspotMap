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

    public function updatePlaces(Application $app)
    {
        $request = $app['request'];

        $placeMapper = $app['PlaceMapper'];
        $places = $request->request->all();

        if ($request->getMethod() == 'POST') {
            foreach ($places as $id => $status) {
                if ($status == "update")
                {
                    $placeUpdated = $placeMapper->findById($id);
                    error_log("copy of"+$placeUpdated->copy_of);
                    $placeOrigin = $placeMapper->findById($placeUpdated->copy_of);
                    $placeMapper->deleteById($id);

                    if ($placeOrigin && $placeUpdated) {
                        error_log("tout roule");
                        $placeUpdated->copy_of = null;
                        $placeUpdated->validated = 1;
                        $placeMapper->save($placeOrigin->fillWith($placeUpdated));
                    }
                    else{
                        error_log("tout roule pas");
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

        if ($request->getMethod() == 'POST') {
            foreach($places as $id => $status)
            {
                if($status == "delete")
                {
                    $commentMapper->deleteById($id);
                }
                else if($status == "validate")
                {
                    $place = $commentMapper->findById($id);
                    if($place != null)
                    {
                        $place->validated = 1;
                        $commentMapper->save($place);
                    }
                }
            }
        }

        return $app->redirect($app['url_generator']->generate('admin'));
    }
}
