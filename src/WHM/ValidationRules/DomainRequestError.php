<?php

namespace WHM\ValidationRules;

use WHM\Exceptions\CPanelErrorException;
use WHM\Interfaces\ValidationRuleInterface;

/**
 * Class DomainRequestError
 * @package WHM\ValidationRules
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
