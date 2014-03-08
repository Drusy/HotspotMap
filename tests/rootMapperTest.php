<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\WebTestCase;


class RootMapperTest extends WebTestCase
{
    public function createApplication()
    {
        include __DIR__.'/../web/index.php';

        return $app;
    }

    public function testRootPage()
    {
        $client = $this->createClient();

        $client->request(
            'GET',
            '/places',
            array(),
            array(),
            array("accept" => "application/json"));


        $this->assertTrue($client->getResponse()->isOk());

        //$this->assertContains("application/json", $client->getResponse()->headers->get('Content-Type'));
        //$this->assertEquals("", $client->getResponse()->getContent());
        //$this->assertEquals("", $client->getResponse()->getStatusCode());
    }
}