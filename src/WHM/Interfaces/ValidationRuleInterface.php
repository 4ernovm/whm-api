<?php

namespace WHM\Interfaces;

/**
 * Interface ValidationRuleInterface
 * @package WHM\Interfaces
 */
interface ValidationRuleInterface
{
    /**
     * @param $response
     * @return boolean
     */
    public function validate($response);
}
