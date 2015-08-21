<?php

namespace WHM\ValidationRules;

use WHM\Interfaces\ValidationRuleInterface;

/**
 * Class AccountRequestError
 * @package WHM\ValidationRules
 */
class AccountRequestError implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws \Exception
     */
    public function validate($response)
    {
        if ($response->result[0]->status != 1) {
            throw new \Exception("Error");
        }
    }
}
