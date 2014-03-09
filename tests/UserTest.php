<?php

use HotspotMap\Model\User;

class UserTest extends PHPUnit_Framework_TestCase
{
    public function testUniqId()
    {
        $user1 = new User();
        $user2 = new User();

        $this->assertNotEquals($user1->getId(), $user2->getId());
    }

    public function isAdminTest()
    {
        $user = new User();

        $user->setRoles("ROLE_ADMIN");

        $this->assertTrue($user->isAdmin());
    }

    public function isAdminNotTest()
    {
        $user = new User();

        $this->assertFalse($user->isAdmin());
    }
}
