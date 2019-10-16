<?php

namespace Cayeye\WhatsAppBusinessApiClient\Exception;

use Symfony\Contracts\HttpClient\ResponseInterface;

class ResponseException extends \Exception
{
    /** @var ResponseInterface */
    private $response;

    /**
     * @param ResponseInterface $response
     * @param string            $message
     * @param int               $code
     */
    public function __construct(ResponseInterface $response, $message = "", $code = 0)
    {
        $this->response = $response;

        parent::__construct($message, $code);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

}