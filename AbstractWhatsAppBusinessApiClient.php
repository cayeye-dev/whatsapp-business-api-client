<?php

namespace Cayeye\WhatsAppBusinessApiClient;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractWhatsAppBusinessApiClient
{
    /** @var array */
    protected $config;
    /** @var HttpClientInterface */
    protected $client;
    /** @var HttpClientInterface */
    public static $staticClient;

    public function __construct(string $authBearer = null, string $url = null)
    {
        $this->config = [
            'base_uri'    => $url,
            'auth_bearer' => $authBearer,
            'headers'     => [
                'Accept' => 'application/json',
            ],
        ];
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->getClient()->request(strtoupper($method), $url, $options);
    }

    public function setClient(HttpClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * @param callable|ResponseInterface|ResponseInterface[]|iterable|null $responseFactory
     * @param string|null                                                  $baseUri
     */
    public static function setMockClient($responseFactory = null, string $baseUri = null): void
    {
        self::$staticClient = new MockHttpClient($responseFactory, $baseUri);
    }

    protected function getClient(): HttpClientInterface
    {
        if (null === $this->client) {
            $this->client = self::$staticClient ?? HttpClient::create($this->config);
        }

        return self::$staticClient ?? $this->client;
    }
}