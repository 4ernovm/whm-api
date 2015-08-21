<?php

namespace Chernoff\WHM\Interfaces;

/**
 * Interface AuthorizationInterface
 * @package Chernoff\WHM\Interfaces
 */
interface AuthorizationInterface
{
    /**
     * Associative array of auth headers where the key is header name and the
     * value is auth string
     *
     * @return array
     */
    public function toArray();

    /**
     * Should return string which can be used in header() function
     *
     * @return string
     */
    public function __toString();
}
