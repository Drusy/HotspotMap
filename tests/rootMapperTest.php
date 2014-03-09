<?php

require_once __DIR__.'/../vendor/autoload.php';

use \HotspotMap\Test\WebTestCase;

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

        $this->jsonRequest($client, 'GET', '/places');
        $this->assertJsonResponse($client->getResponse());
    }
}