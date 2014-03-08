<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\WebTestCase;


class RootMapperTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../web/index.php';
        //return new Silex\Application();
    }

    public function testRootPage()
    {
        $this->assertEquals("", $_SERVER['HTTP_ACCEPT']);
    }
}