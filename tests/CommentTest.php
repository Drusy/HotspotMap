<?php

use HotspotMap\Model\Comment;

class CommentTest extends PHPUnit_Framework_TestCase
{
    public function testUniqId()
    {
        $comment1 = new Comment();
        $comment2 = new Comment();

        $this->assertNotEquals($comment1->getId(), $comment2->getId());
    }
}
