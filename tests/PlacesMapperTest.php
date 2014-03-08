<?php

class PlacesMapperTest extends PHPUnit_Extensions_Database_TestCase
{
    // instancie pdo seulement une fois pour le nettoyage du test/le chargement de la fixture
    static private $pdo = null;

    // instancie PHPUnit_Extensions_Database_DB_IDatabaseConnection seulement une fois par test
    private $conn = null;

    public function __construct() {

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

    public function testDataSet()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('place'));
    }

    public function testAddPlace() {
        //$placesController = new \HotspotMap\Controller\PlacesController();

        //$placesController->addPlace()
    }
}