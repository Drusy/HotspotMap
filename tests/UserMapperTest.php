<?php

Use \HotspotMap\Model\User;

class UserMapperTest extends PHPUnit_Extensions_Database_TestCase
{
    private static $pdo = null;
    private $conn = null;
    private $app;
    private $userMapper;

    public function setUp()
    {
        parent::setUp();
        $GLOBALS['TEST_MODE'] = true;

        include __DIR__.'/../web/index.php';

        $this->app = $app;
        $this->userMapper = $this->app['UserMapper'];
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
        return $this->createFlatXmlDataSet("tests/mock/users.xml");
    }

    public function testPlacesDataSet()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('user'));
    }

    public function testFindAllUser()
    {
        $users = $this->userMapper->findAll();

        $this->assertEquals(2, count($users));
    }

    public function testFindByUsernameUser()
    {
        $user = $this->userMapper->findByUsername("Drusy");

        $this->assertEquals(1, $user->getId());
    }

    public function testAddUser()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('user'));

        $user = new User();
        $user->username = "testUsername";
        $this->userMapper->save($user);

        $this->assertEquals(3, $this->getConnection()->getRowCount('user'));
    }

    public function testFindUpdateUser()
    {
        $user = $this->userMapper->findById(1);

        $this->assertEquals('Drusy', $user->username);

        $user->username = 'testUsername';
        $this->userMapper->save($user);

        $this->assertEquals('testUsername', $user->username);
    }

    public function testBadFindUserById()
    {
        $user = $this->userMapper->findById(-1);

        $this->assertNull($user);
    }

    public function testBadFindUserByUsername()
    {
        $user = $this->userMapper->findByUsername("");

        $this->assertNull($user);
    }
}
