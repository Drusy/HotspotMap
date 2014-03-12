<?php

Use \HotspotMap\Model\Comment;

class CommentMapperTest extends PHPUnit_Extensions_Database_TestCase
{
    private static $pdo = null;
    private $conn = null;
    private $app;
    private $commentMapper;

    public function setUp()
    {
        parent::setUp();
        $GLOBALS['TEST_MODE'] = true;

        include __DIR__.'/../web/index.php';

        $this->app = $app;
        $this->commentMapper = $this->app['CommentMapper'];
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
        return $this->createFlatXmlDataSet("tests/mock/comments.xml");
    }

    public function testCommentsDataSet()
    {
        $this->assertEquals(3, $this->getConnection()->getRowCount('comment'));
    }

    public function testValidatedComments()
    {
        $comments = $this->commentMapper->findValidated();

        $this->assertEquals(2, count($comments));
    }

    public function testNonValidatedComments()
    {
        $comments = $this->commentMapper->findNonValidated();

        $this->assertEquals(1, count($comments));
    }

    public function testAddRemoveComment()
    {
        $this->assertEquals(3, $this->getConnection()->getRowCount('comment'));

        $comment = new Comment();
        $comment->content = "testContent";
        $this->commentMapper->save($comment);

        $this->assertEquals(4, $this->getConnection()->getRowCount('comment'));

        $this->commentMapper->deleteById($comment->getId());

        $this->assertEquals(3, $this->getConnection()->getRowCount('comment'));
    }

    public function testFindUpdateComment()
    {
        $comment = $this->commentMapper->findById(1);

        $this->assertEquals('Comment 1', $comment->content);

        $comment->content = 'testContent';
        $this->commentMapper->save($comment);

        $this->assertEquals('testContent', $comment->content);
    }

    public function testBadFindComment()
    {
        $comment = $this->commentMapper->findById(-1);

        $this->assertNull($comment);
    }
}
