<?php

namespace Chernoff\WHM\Interfaces;

/**
 * Interface ValidationRuleInterface
 * @package Chernoff\WHM\Interfaces
 */
interface ValidationRuleInterface
{
    /**
     * @param $response
     * @return boolean
     */
    public function validate($response);
}
