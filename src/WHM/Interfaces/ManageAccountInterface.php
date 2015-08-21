<?php

namespace WHM\Interfaces;

/**
 * Interface ManageAccountInterface
 * @package WHM\Interfaces
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
