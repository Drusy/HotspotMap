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
            foreach($places as $id => $status)
            {
                if($status == "validate")
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
        $places = $request->request->all();

        if ($request->getMethod() == 'POST') {
            foreach($places as $id => $status)
            {
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
        $commentMapper = $app['CommentMapper'];
        $data["nonvalidated"] = $placeMapper->findAllNonValidated();
        $data["validated"] = $placeMapper->findAllValidated();
        $data["comments"] = $commentMapper->findAllNonValidatedComment();

        return $this->respond($app, 'data', $data, 'admin/admin');
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
