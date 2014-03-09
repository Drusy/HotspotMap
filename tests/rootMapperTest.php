<?php

use \HotspotMap\Test\WebTestCase;

class rootMapperTest extends WebTestCase
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
