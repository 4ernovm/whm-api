<?php

namespace Chernoff\WHM\ValidationRules;

use Chernoff\WHM\Exceptions\CPanelErrorException;
use Chernoff\WHM\Interfaces\ValidationRuleInterface;

/**
 * Class DomainRequestError
 * @package Chernoff\WHM\ValidationRules
 */
class DomainRequestError implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws CPanelErrorException
     */
    public function validate($response)
    {
        if (!empty($response->cpanelresult->data[0]->result) &&
            $response->cpanelresult->data[0]->result == 0) {

            throw new CPanelErrorException($response->cpanelresult->data[0]->reason);
        }
    }
}
