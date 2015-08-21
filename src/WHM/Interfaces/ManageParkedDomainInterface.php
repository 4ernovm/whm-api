<?php

namespace Chernoff\WHM\Interfaces;

/**
 * Interface ManageParkedDomainInterface
 * @package Chernoff\WHM\Interfaces
 */
interface ManageParkedDomainInterface
{
    /**
     * @param string $username
     * @return array
     */
    public function domainsGetParked($username);

    /**
     * @param string $domain
     * @param string $username
     * @return mixed
     */
    public function domainPark($domain, $username);

    /**
     * @param string $domain
     * @param string $username
     * @return mixed
     */
    public function domainParked($domain, $username);

    /**
     * @param string $domain
     * @param string $username
     * @return mixed
     */
    public function domainUnpark($domain, $username);
}
