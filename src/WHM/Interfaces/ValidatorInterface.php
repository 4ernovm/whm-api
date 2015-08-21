<?php

namespace WHM\Interfaces;

/**
 * Interface ValidatorInterface
 * @package WHM\Interfaces
 */
interface ValidatorInterface
{
    /**
     * @param $response
     * @param WHMValidationRuleInterface[] $rules
     * @return mixed
     */
    public function validate($response, array $rules);
}
