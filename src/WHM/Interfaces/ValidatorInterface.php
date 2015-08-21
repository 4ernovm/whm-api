<?php

namespace Chernoff\WHM\Interfaces;

/**
 * Interface ValidatorInterface
 * @package Chernoff\WHM\Interfaces
 */
interface ValidatorInterface
{
    /**
     * @param $response
     * @param ValidationRuleInterface[] $rules
     * @return mixed
     */
    public function validate($response, array $rules);
}
