<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Exceptions\CPanelNotFoundException;
use Chernoff\WHM\Exceptions\WHMEmptyResponseException;
use Chernoff\WHM\Interfaces\AuthorizationInterface;
use Chernoff\WHM\Interfaces\DeployerInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Post\PostBody;

use Exception;
use GuzzleHttp\Url;

/**
 * Class GuzzleDeployer
 * @package Chernoff\WHM
 */
class GuzzleDeployer implements DeployerInterface
{
    /** @var  \GuzzleHttp\Client */
    protected $client;

    /** @var  AuthorizationInterface|null */
    protected $auth;

    /** @var string  */
    protected $protocol = 'https';

    /** @var  string */
    protected $host;

    /** @var  int */
    protected $port;

    /** @var  array */
    protected $defaultOptions;

    /**
     * @param Client $client
     * @param array $defaultOptions
     */
    public function __construct(Client $client, array $defaultOptions = array()) {
        $this->client = $client;
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * @param AuthorizationInterface|null $auth
     * @throws Exception
     * @return DeployerInterface
     */
    public function setAuth(AuthorizationInterface $auth = null)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @return AuthorizationInterface|null
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
     * @param array $options
     * @return mixed
     * @throws CPanelNotFoundException
     * @throws Exception
     * @throws WHMEmptyResponseException
     */
    public function send($uri, array $query = array(), array $body = array(), $method = 'GET', $options = array()) {
        $url     = $this->getRequestUrl($uri);
        $options = $this->getDefaultRequestOptions($options);

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
            $request = $this->client->createRequest($method, $url, $options);

            if (!empty($this->auth)) {
                $request->setHeaders($this->auth->toArray());
            }

            if (!empty($postBody)) {
                $request->setBody($postBody);
            }

            /** @var ResponseInterface $response */
            $response = $this->client->send($request);

            return $response->json(["object" => true]);
        }
        catch (RequestException $e) {
            $response = $e->getResponse();

            if ($response) {
                switch ($response->getStatusCode()) {
                    case 0:
                        throw new CPanelNotFoundException("cPanel Not Found");

                    case 403:
                        throw new Exception("Invalid Creds");

                    default:
                        throw new Exception("cPanel Status: " . $response->getStatusCode());
                }
            }
            else {
                throw new WHMEmptyResponseException($e->getMessage());
            }
        }
        catch (Exception $e) {
            // Try again in case of SSH connection issues
            // @TODO add retries limitation
            if (substr_count($e->getMessage(), "SSH")) {
                return $this->send($uri, $query, $body, $method);
            }

            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * All settings has been taken from previous implementation.
     *
     * @param array $options
     * @return array
     */
    public function getDefaultRequestOptions($options = array())
    {
        return array_replace_recursive($this->defaultOptions, $options);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setDefaultRequestOptions(array $options)
    {
        $this->defaultOptions = $options;

        return $this;
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

        return new Url($this->protocol, $this->host, null, null, $this->port, $uri);
    }
}
