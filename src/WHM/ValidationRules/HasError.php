<?php

namespace WHM\ValidationRules;

use WHM\Exceptions\CPanelErrorException;
use WHM\Interfaces\ValidationRuleInterface;

/**
 * Class HasError
 * @package WHM\ValidationRules
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
