<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Exceptions\CPanelNotFoundException;
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

    /** @var  array */
    protected $credentials;

    protected $requiredKeys = array("host", "port", "username", "password");

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * @param array $credentials
     * @throws Exception
     * @return DeployerInterface
     */
    public function setCredentials(array $credentials)
    {
        $this->isValid($credentials);
        $this->credentials = $credentials;

        return $this;
    }

    /**
     * @return array
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Ensure that we have all information we need.
     *
     * @param array $credentials
     * @throws Exception
     */
    protected function isValid(array $credentials)
    {
        if ($keys = array_diff_key(array_flip($this->requiredKeys), $credentials)) {
            throw new Exception(implode(',', $keys) . " is/are required");
        }
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
        $this->isValid($this->credentials);

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
            $request = $this->client->createRequest($method, $url, $options);

            return json_decode($this->client->send($request)->getBody(true));
        }
        catch (RequestException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 0:
                    throw new CPanelNotFoundException("cPanel Not Found");

                case 403:
                    throw new Exception("Invalid Creds");

                default:
                    throw new Exception("cPanel Status: " . $e->getResponse()->getStatusCode());
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
            "auth" => array($this->credentials["username"], $this->credentials["password"], "Basic"),
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
     */
    protected function getRequestUrl($uri)
    {
        // Remove leading and trailing slashes.
        $uri  = trim($uri, DIRECTORY_SEPARATOR);
        $base = "https://{$this->credentials["host"]}:{$this->credentials["port"]}";

        return $base . DIRECTORY_SEPARATOR . $uri;
    }
}
