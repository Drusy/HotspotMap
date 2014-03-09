<?php

namespace HotspotMap\Test;

use HotspotMap\Test\WebTestCaseJson as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    protected function assertXmlResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/xml'),
            $response->headers
        );
        $this->assertTrue($response->isOk());
    }

    protected function xmlRequest($client, $verb, $endpoint)
    {
        return $client->request($verb, $endpoint,
            array(),
            array(),
            array(
                'HTTP_ACCEPT'  => 'application/xml',
                'CONTENT_TYPE' => 'application/xml'
            )
        );
    }
}
