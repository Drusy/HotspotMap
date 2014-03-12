<?php

Use \HotspotMap\Model\Place;

class PlaceMapperTest extends PHPUnit_Extensions_Database_TestCase
{
    private static $pdo = null;
    private $conn = null;
    private $app;
    private $placeMapper;

    public function setUp()
    {
        parent::setUp();
        $GLOBALS['TEST_MODE'] = true;

        include __DIR__.'/../web/index.php';

        $this->app = $app;
        $this->placeMapper = $this->app['PlaceMapper'];
    }

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = \HotspotMap\Service\Connection::getConnection();
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    public function getDataSet()
    {
        return $this->createFlatXmlDataSet("tests/mock/places.xml");
    }

    public function testPlacesDataSet()
    {
        $this->assertEquals(3, $this->getConnection()->getRowCount('place'));
    }

    public function testValidatedPlaces()
    {
        $places = $this->placeMapper->findValidated();

        $this->assertEquals(2, count($places));
    }

    public function testNonValidatedPlaces()
    {
        $places = $this->placeMapper->findNonValidated();

        $this->assertEquals(1, count($places));
    }

    public function testAddRemovePlace()
    {
        $this->assertEquals(3, $this->getConnection()->getRowCount('place'));

        $place = new Place();
        $place->address = "testAddress";
        $this->placeMapper->save($place);

        $this->assertEquals(4, $this->getConnection()->getRowCount('place'));

        $this->placeMapper->deleteById($place->getId());

        $this->assertEquals(3, $this->getConnection()->getRowCount('place'));
    }

    public function testFindUpdatePlace()
    {
        $place = $this->placeMapper->findById(1);

        $this->assertEquals('ISIMA', $place->name);

        $place->name = 'testName';
        $this->placeMapper->save($place);

        $this->assertEquals('testName', $place->name);
    }

    public function testBadFindPlace()
    {
        $place = $this->placeMapper->findById(-1);

        $this->assertNull($place);
    }

    public function testClosestPlaces()
    {
        // ISIMA
        $isima = $this->placeMapper->findById(1);

        // Too close
        $places = $this->placeMapper->findClosestPlaces($isima->latitude, $isima->longitude, 1);
        $this->assertEquals(1, count($places));

        // OK
        $places = $this->placeMapper->findClosestPlaces($isima->latitude, $isima->longitude, 5);
        $this->assertEquals(2, count($places));
    }
}
