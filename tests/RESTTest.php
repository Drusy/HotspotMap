<?php

use \HotspotMap\Test\WebTestCase;

class RESTTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $GLOBALS['TEST_MODE'] = true;
    }

    public function createApplication()
    {
        include __DIR__.'/../web/index.php';

        return $app;
    }

    public function testJsonRootPage()
    {
        $client = $this->createClient();

        $this->jsonRequest($client, 'GET', '/');
        $this->assertJsonResponse($client->getResponse());
    }

    public function testXmlRootPage()
    {
        $client = $this->createClient();
    }
}
