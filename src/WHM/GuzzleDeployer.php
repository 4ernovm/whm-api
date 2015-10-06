<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Exceptions\CPanelNotFoundException;
use Chernoff\WHM\Exceptions\WHMEmptyResponseException;
use Chernoff\WHM\Interfaces\AuthorizationInterface;
use Chernoff\WHM\Interfaces\DeployerInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Post\PostBody;

use Exception;

/**
 * Class GuzzleDeployer
 * @package Chernoff\WHM
 */
class GuzzleDeployer implements DeployerInterface
{
    /** @var  \GuzzleHttp\Client */
    protected $client;

    /** @var  AuthorizationInterface */
    protected $auth;

    /**
     * @var string
     */
    protected $protocol = 'https';

    /** @var  string */
    protected $host;

    /** @var  int */
    protected $port;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * @param AuthorizationInterface $auth
     * @throws Exception
     * @return DeployerInterface
     */
    public function setAuth(AuthorizationInterface $auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @return AuthorizationInterface
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $protocol
     * @return $this
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $uri
     * @param array $query
     * @param array $body
     * @param string $method
     * @return mixed
     * @throws CPanelNotFoundException
     * @throws Exception
     */
    public function send($uri, array $query = array(), array $body = array(), $method = 'GET') {
        $url     = $this->getRequestUrl($uri);
        $options = $this->getDefaultRequestOptions();

        if ($query) {
            $options["query"] = $query;
        }

        if ($body) {
            $postBody = new PostBody();
            $postBody->forceMultipartUpload(true);
            $postBody->replaceFields($body);
        }

        try {
            /** @var RequestInterface $request */
            $request = $this->client->createRequest($method, $url, $options)->setHeaders($this->auth->toArray());

            if (!empty($postBody)) {
                $request->setBody($postBody);
            }

            return json_decode($this->client->send($request)->getBody(true));
        }
        catch (RequestException $e) {
            $response = $e->getResponse();

            if ($response) {
                switch ($e->getResponse()->getStatusCode()) {
                    case 0:
                        throw new CPanelNotFoundException("cPanel Not Found");

                    case 403:
                        throw new Exception("Invalid Creds");

                    default:
                        throw new Exception("cPanel Status: " . $e->getResponse()->getStatusCode());
                }
            }
            else {
                throw new WHMEmptyResponseException("WHM response is empty");
            }
        }
    }

    /**
     * All settings has been taken from previous implementation.
     *
     * @return array
     */
    protected function getDefaultRequestOptions()
    {
        return array(
            "config" => array(
                "curl" => array(
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_HEADER         => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_TIMEOUT        => 20,
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_FOLLOWLOCATION => true,
                ),
            ),
        );
    }

    /**
     * @param string $uri
     * @return string
     * @throws Exception
     */
    protected function getRequestUrl($uri)
    {
        if (empty($this->host) || empty($this->port)) {
            throw new Exception("Host and port are required");
        }

        // Remove leading and trailing slashes.
        $uri = trim($uri, '/');

        return "{$this->protocol}://{$this->host}:{$this->port}/{$uri}";
    }
}
