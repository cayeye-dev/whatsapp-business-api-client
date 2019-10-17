<?php

namespace Cayeye\Tests;

use Cayeye\WhatsAppBusinessApiClient\WhatsAppBusinessApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class WhatsAppBusinessApiClientTest extends TestCase
{
    /** @var WhatsAppBusinessApiClient */
    private $client;

    public function test()
    {
        $responseContent = '{"url": "http://domain.com/image.jpg"}';

        WhatsAppBusinessApiClient::setMockClient([new MockResponse($responseContent)], 'http://localhost');
        $client = new WhatsAppBusinessApiClient();

        $response = $client->getMedia('foo');

        self::assertEquals($responseContent, $response->getContent());
    }
}
