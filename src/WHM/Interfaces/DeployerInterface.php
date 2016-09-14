<?php

namespace Chernoff\WHM\Interfaces;

/**
 * Interface DeployerInterface
 * @package Chernoff\WHM\Interfaces
 */
interface DeployerInterface
{
    /**
     * @param AuthorizationInterface $auth
     * @return self
     */
    public function setAuth(AuthorizationInterface $auth);

    /**
     * @return AuthorizationInterface
     */
    public function getAuth();

    /**
     * @param array $options
     * @return array
     */
    public function getDefaultRequestOptions($options = array());

    /**
     * @param array $options
     * @return $this
     */
    public function setDefaultRequestOptions(array $options);

    /**
     * @param string $uri
     * @param array $arguments
     * @param array $body
     * @param string $method
     * @return mixed
     */
    public function send($uri, array $arguments = array(), array $body = array(), $method = 'GET');
}
