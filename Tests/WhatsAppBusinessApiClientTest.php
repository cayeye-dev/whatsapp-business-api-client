<?php

namespace Cayeye\Tests;

use Cayeye\WhatsAppBusinessApiClient\WhatsAppBusinessApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WhatsAppBusinessApiClientTest extends TestCase
{
    /** @var HttpClientInterface */
    private $httpClient;
    /** @var WhatsAppBusinessApiClient */
    private $waClient;

    public function setUp(): void
    {
        $this->httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $this->waClient = new WhatsAppBusinessApiClient();
        $this->waClient->setClient($this->httpClient);
    }

    public function testSendMessage()
    {
        $userId = '123';
        $text = 'foobar';

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/v1/messages',
                [
                    'json' => [
                        'text'           => ['body' => $text],
                        'to'             => $userId,
                        'type'           => 'text',
                        'recipient_type' => 'individual',
                    ],
                ]
            );

        $this->waClient->sendMessage($userId, $text);
    }

    public function testSendMessageWithContact()
    {
        $userId = '123';
        $name = 'foobar';
        $phone1 = '3434343434';
        $phone2 = '5656565656';

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/v1/messages',
                [
                    'json' => [
                        'to'             => $userId,
                        'recipient_type' => 'individual',
                        'type'           => 'contacts',
                        'contacts'       => [
                            [
                                'name'   => ['formatted_name' => $name],
                                'phones' => [
                                    ['phone' => $phone1],
                                    ['phone' => $phone2],
                                ],
                            ],
                        ],
                    ],
                ]
            );

        $this->waClient->sendMessageWithContact($userId, $name, [$phone1, $phone2]);
    }

    public function testGetProfilePhoto()
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                '/v1/settings/profile/photo',
                []
            );

        $this->waClient->getProfilePhoto(true);
    }

    public function testGetProfilePhotoWithLink()
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                '/v1/settings/profile/photo',
                [
                    'format' => 'link'
                ]
            );

        $this->waClient->getProfilePhoto();
    }
}
