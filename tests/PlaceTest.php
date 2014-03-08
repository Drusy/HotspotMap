<?php

use HotspotMap\Model\Place;

class PlacesControllerTest extends PHPUnit_Framework_TestCase
{
    public function testUniqId()
    {
        $place1 = new Place();
        $place2 = new Place();

        $this->assertNotEquals($place1->getId(), $place2->getId());
    }
}
