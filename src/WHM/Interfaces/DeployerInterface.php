<?php

namespace Chernoff\WHM\Interfaces;

/**
 * Interface DeployerInterface
 * @package Chernoff\WHM\Interfaces
 */
interface DeployerInterface
{
    /**
     * @param array $credentials
     * @return self
     */
    public function setCredentials(array $credentials);

    /**
     * @return array
     */
    public function getCredentials();

    /**
     * @param string $uri
     * @param array $arguments
     * @param array $body
     * @param string $method
     * @return mixed
     */
    public function send($uri, array $arguments = array(), array $body = array(), $method = 'GET');
}
