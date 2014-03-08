<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;

class PlacesMapperTest extends PHPUnit_Extensions_Database_TestCase
{
    static private $pdo = null;
    private $conn = null;
    private $app;
    private $placeMapper;

    public function setUp() {
        $GLOBALS['TEST_MODE'] = true;

        include __DIR__.'/../web/index.php';

        $this->app = $app;
        $this->placeMapper = $this->app['PlaceMapper'];
    }

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
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
        $this->assertEquals(2, $this->getConnection()->getRowCount('place'));
    }

    public function testValidatedPlacesDataSet()
    {
        $places = $this->placeMapper->findAllValidated();

        $this->assertEquals(1, count($places));
    }

    public function testNonValidatedPlacesDataSet()
    {
        $places = $this->placeMapper->findAllNonValidated();

        $this->assertEquals(1, count($places));
    }
}