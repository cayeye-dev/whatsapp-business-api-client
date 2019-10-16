<?php

namespace Cayeye\WhatsAppBusinessApiClient;

use Cayeye\WhatsAppBusinessApiClient\Exception\ResponseException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @see https://developers.facebook.com/docs/whatsapp/api/reference
 *      https://developers.facebook.com/docs/whatsapp/api/errors
 */
class WhatsAppBusinessApiClient
{
    /** @var bool */
    protected $async = false;
    /** @var HttpClient */
    protected $client;
    /** @var HttpClient */
    public static $staticClient;

    public function __construct(string $authBearer, string $url = null)
    {
        $config = [
            'base_uri'    => $url,
            'auth_bearer' => $authBearer,
            'headers'     => [
                'Accept' => 'application/json',
            ],
        ];

        $this->client = self::$staticClient ?? HttpClient::create($config);
    }

    /**
     * @param string $id
     *
     * @return array|ResponseInterface|null
     */
    public function getMedia(string $id)
    {
        return $this->client->request('GET', sprintf('/v1/media/%s', $id));
    }

    /**
     * @param string $binary
     * @param string $mimeType
     *
     * @return array|ResponseInterface|null
     */
    public function postMedia(string $binary, string $mimeType)
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
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return ResponseInterface|array|null
     */
    public function request(string $method, string $url, array $options = [])
    {
        $response = $this->client->request(strtoupper($method), $url, $options);

        if (false === $this->async) {
            return self::computeResponse($response);
        } else {
            return $response;
        }
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

    public function setAsync(bool $async)
    {
        $this->async = $async;
    }

    /**
     * @param callable|ResponseInterface|ResponseInterface[]|iterable|null $responseFactory
     * @param string|null                                                  $baseUri
     */
    public static function setMockClient($responseFactory = null, string $baseUri = null)
    {
        self::$staticClient = new MockHttpClient($responseFactory, $baseUri);
    }
}