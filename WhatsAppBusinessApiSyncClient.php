<?php

namespace Cayeye\WhatsAppBusinessApiClient;

use Cayeye\WhatsAppBusinessApiClient\Exception\ResponseException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @see https://developers.facebook.com/docs/whatsapp/api/reference
 *      https://developers.facebook.com/docs/whatsapp/api/errors
 */
class WhatsAppBusinessApiSyncClient
{
    /** @var bool */
    protected $async = false;
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

    /**
     * @param string $id
     *
     * @return ResponseInterface
     */
    public function getMedia(string $id): ResponseInterface
    {
        return $this->getClient()->request('GET', sprintf('/v1/media/%s', $id));
    }

    /**
     * @param string $binary
     * @param string $mimeType
     *
     * @return array|ResponseInterface|null
     */
    public function uploadMedia(string $binary, string $mimeType)
    {
        $options = [
            'headers' => ['content-type' => $mimeType],
            'body'    => $binary,
        ];

        return $this->request('POST', '/v1/media', $options);
    }

    /**
     * @return ResponseInterface|array|null
     */
    public function getSettings()
    {
        return $this->request('GET', '/v1/settings/application');
    }

    /**
     * @param string $url
     *
     * @return ResponseInterface|array|null
     */
    public function updateWebhook(string $url)
    {
        $data = [
            'webhooks' => ['url' => $url],
        ];

        $options = [
            'json' => $data,
        ];

        return $this->request('PATCH', '/v1/settings/application', $options);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return ResponseInterface|array|null
     */
    public function request(string $method, string $url, array $options = [])
    {
        $response = $this->getClient()->request(strtoupper($method), $url, $options);

        if (false === $this->async) {
            return self::computeResponse($response);
        } else {
            return $response;
        }
    }

    public function setAsync(bool $async): void
    {
        $this->async = $async;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array|string|null
     */
    public static function computeResponse(ResponseInterface $response)
    {
        $headers = $response->getHeaders();

        $isJson = 0 === strpos($contentType = implode(';', $headers['content-type'] ?? ''), 'application/json');

        $responseData = $isJson ? $response->toArray(false) : $response->getContent(false);

        if (400 <= $response->getStatusCode()) {
            throw new ResponseException($response);
        }

        return $responseData;
    }

    /**
     * @param callable|ResponseInterface|ResponseInterface[]|iterable|null $responseFactory
     * @param string|null                                                  $baseUri
     */
    public static function setMockClient($responseFactory = null, string $baseUri = null): void
    {
        self::$staticClient = new MockHttpClient($responseFactory, $baseUri);
    }

    private function getClient(): HttpClientInterface
    {
        if (null === $this->client) {
            $this->client = self::$staticClient ?? HttpClient::create($this->config);
        }

        return self::$staticClient ?? $this->client;
    }
}