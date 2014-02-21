<?php

namespace HotspotMap\Service;

use HotspotMap\Model\Place;

class PlaceMapper extends Mapper
{
    private $countByIdQuery = 'SELECT COUNT(*) FROM Place WHERE id = :id';
    private $findByIdQuery = 'SELECT * FROM Place WHERE id = :id';
    private $findAllQuery = 'SELECT * FROM Place';
    private $insertQuery = 'INSERT INTO Place Values (
        :id,
        :longitude,
        :latitude,
        :name,
        :address,
        :country,
        :town,
        :website)';
    private $updateQuery = 'UPDATE Place
        SET
        longitude = :longitude,
        latitude = :latitude,
        name = :name,
        address = :address,
        country = :country,
        town = :town
        website = :website
        WHERE id = :id';

    private $closestPlaces = 'SELECT id, name,
( 6371 * acos( cos( radians( :lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( :long) ) + sin( radians( :lat ) ) * sin( radians( latitude ) ) ) )
AS distance
FROM Place
HAVING distance < :dist
ORDER BY distance
LIMIT 0 , 10';

    private function fillPlace($placeTab)
    {
        $place = new Place($placeTab['id']);
        $place->name = $placeTab['name'];
        $place->address = $placeTab['address'];
        $place->longitude = $placeTab['longitude'];
        $place->latitude = $placeTab['latitude'];
        $place->country = $placeTab['country'];
        $place->town = $placeTab['town'];
        $place->setWebsite($placeTab['website']);

        return $place;
    }

    public function save(Place $place)
    {
        $exist = $this->con->selectQuery($this->countByIdQuery, [
            'id' => $place->getId()
        ]);

        if ($exist[0][0] == 1) {
            $res = $this->con->executeQuery($this->updateQuery, [
                'id' => $place->getId(),
                'name' => $place->name,
                'address' => $place->address,
                'longitude' => $place->longitude,
                'latitude' => $place->latitude,
                'country' => $place->country,
                'town' => $place->town,
                'website' => $place->getWebsite()
            ]);
        } else {
            $res = $this->con->executeQuery($this->insertQuery, [
                'id' => $place->getId(),
                'name' => $place->name,
                'address' => $place->address,
                'longitude' => $place->longitude,
                'latitude' => $place->latitude,
                'country' => $place->country,
                'town' => $place->town,
                'website' => $place->getWebsite()
            ]);
        }

        return $res;
    }

    public function findById($id)
    {
        $placeTab = $this->con->selectQuery($this->findByIdQuery, [
            'id' => $id
        ]);

        return $this->fillPlace($placeTab[0]);
    }

    public function findAll()
    {
        $placeTab = $this->con->selectQuery($this->findAllQuery);
        $placeList = [];

        if (!empty($placeTab)) {
            foreach ($placeTab as $place) {
                $placeList[] = $this->fillPlace($place);
            }
        }

        return $placeList;
    }

    public function findClosestPlaces($latitude, $longitude, $distance)
    {
        $placeTab = $this->con->selectQuery($this->closestPlaces, [
            'lat' => $latitude,
            'long' => $longitude,
            'dist' => $distance
        ]);
        $placeList = [];

        if (!empty($placeTab)) {
            foreach ($placeTab as $place) {
                $placeList[] = $this->fillPlace($place);
            }
        }

        return $placeList;
    }
}
