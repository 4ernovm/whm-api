<?php

namespace Chernoff\WHM\Interfaces;

/**
 * Interface ManageAccountInterface
 * @package Chernoff\WHM\Interfaces
 */
interface ManageAccountInterface
{
    /**
     * @param string $username
     * @param integer $limit
     * @return mixed
     */
    public function addonDomainLimitSet($username, $limit);
}
