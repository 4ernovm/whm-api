<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Interfaces\ValidationRuleInterface;
use Chernoff\WHM\Interfaces\ValidatorInterface;

/**
 * Class Validator
 * @package Chernoff\WHM
 */
class Validator implements ValidatorInterface
{
    /**
     * @param $response
     * @param array $rules
     * @return mixed|void
     * @throws \Exception
     */
    public function validate($response, array $rules)
    {
        foreach ($rules as $rule) {
            if (!($rule instanceof ValidationRuleInterface)) {
                throw new \Exception("Invalid rule given");
            }

            $rule->validate($response);
        }
    }
}
