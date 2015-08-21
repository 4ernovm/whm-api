<?php

namespace Chernoff\WHM\ValidationRules;

use Chernoff\WHM\Exceptions\CPanelErrorException;
use Chernoff\WHM\Interfaces\ValidationRuleInterface;

/**
 * Class HasError
 * @package Chernoff\WHM\ValidationRules
 */
class HasError implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws CPanelErrorException
     */
    public function validate($response)
    {
        if (!empty($response->cpanelresult->error)) {
            throw new CPanelErrorException($response->cpanelresult->error);
        }
    }
}
