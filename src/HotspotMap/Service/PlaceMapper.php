<?php

namespace HotspotMap\Service;

use HotspotMap\Model\Place;

class PlaceMapper extends Mapper
{
    private $countByIdQuery = 'SELECT COUNT(*) FROM Place WHERE id = :id';
    private $findByIdQuery = 'SELECT * FROM Place WHERE id = :id';
    private $findAllQuery = 'SELECT * FROM Place WHERE validated = :validated';
    private $findUpdatedQuery = 'SELECT * FROM Place WHERE copy_of IS NOT NULL';
    private $insertQuery = 'INSERT INTO Place Values (
        :id,
        :latitude,
        :longitude,
        :address,
        :country,
        :town,
        :name,
        :website,
        :description,
        :validated,
        :copy_of)';
    private $updateQuery = 'UPDATE Place
        SET
        longitude = :longitude,
        latitude = :latitude,
        name = :name,
        address = :address,
        country = :country,
        town = :town,
        website = :website,
        description = :description,
        validated = :validated,
        copy_of = :copy_of
        WHERE id = :id';

    private $closestPlaces = 'SELECT *,
        ( 6371 * acos( cos( radians( :lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) -
        radians( :lng) ) + sin( radians( :lat ) ) * sin( radians( latitude ) ) ) )
        AS distance
        FROM Place
        WHERE validated = :validated
        HAVING distance < :dist
        ORDER BY distance
        LIMIT 0 , 10';

    private $delete = 'DELETE
     FROM Place
     WHERE id = :id';

    private function fillPlace($placeTab)
    {
        $place = new Place($placeTab['id']);
        $place->name = $placeTab['name'];
        $place->address = $placeTab['address'];
        $place->longitude = $placeTab['longitude'];
        $place->latitude = $placeTab['latitude'];
        $place->country = $placeTab['country'];
        $place->town = $placeTab['town'];
        $place->description = $placeTab['description'];
        $place->distance = round($placeTab['distance'], 1, PHP_ROUND_HALF_EVEN);
        $place->setWebsite($placeTab['website']);
        $place->validated = $placeTab['validated'];
        $place->copy_of = $placeTab['copy_of'];

        return $place;
    }

    public function save(Place $place)
    {
        $exist = $this->con->selectQuery($this->countByIdQuery, [
            'id' => $place->getId()
        ]);

        $placeArray = array(
            'id' => $place->getId(),
            'name' => $place->name,
            'address' => $place->address,
            'longitude' => $place->longitude,
            'latitude' => $place->latitude,
            'country' => $place->country,
            'town' => $place->town,
            'website' => $place->getWebsite(),
            'description' => $place->description,
            'validated' => $place->validated,
            'copy_of'   => $place->copy_of
        );

        if ($exist[0][0] == 1) {
            $res = $this->con->executeQuery($this->updateQuery, $placeArray);
        } else {
            $res = $this->con->executeQuery($this->insertQuery, $placeArray);
        }

        return $res;
    }

    public function findById($id)
    {
        $placeTab = $this->con->selectQuery($this->findByIdQuery, [
            'id' => $id
        ]);

        if ($placeTab == null) {
            return null;
        }

        return $this->fillPlace($placeTab[0]);
    }

    public function findValidated()
    {
        return $this->findAll(true);
    }

    public function findNonValidated()
    {
        return $this->findAll(false);
    }

    public function findUpdated()
    {
        $placeTab = $this->con->selectQuery($this->findUpdatedQuery);
        $placeList = [];

        if (!empty($placeTab)) {
            foreach ($placeTab as $place) {
                $placeList[] = $this->fillPlace($place);
            }
        }

        return $placeList;
    }

    protected function findAll($validated = true)
    {
        $placeTab = $this->con->selectQuery($this->findAllQuery, [
            'validated' => $validated
        ]);
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
            'lng' => $longitude,
            'dist' => $distance,
            'validated' => 1
        ]);
        $placeList = [];

        if (!empty($placeTab)) {
            foreach ($placeTab as $place) {
                $placeList[] = $this->fillPlace($place);
            }
        }

        return $placeList;
    }

    public function deleteById($id)
    {
        return $this->con->executeQuery($this->delete, [
            'id' => $id
        ]);
    }
}
