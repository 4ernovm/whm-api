<?php

namespace WHM\Interfaces;

/**
 * Interface ManageAddonDomainInterface
 * @package WHM\Interfaces
 */
interface ManageAddonDomainInterface
{
    /**
     * @param string $username
     * @return array
     */
    public function domainsGetAddon($username);

    /**
     * @param string $domain
     * @param string $username
     * @return mixed
     */
    public function domainAdd($domain, $username);

    /**
     * @param string $domain
     * @param string $username
     * @return mixed
     */
    public function domainAdded($domain, $username);

    /**
     * @param string $domain
     * @param string $username
     * @return mixed
     */
    public function domainRemove($domain, $username);
}
