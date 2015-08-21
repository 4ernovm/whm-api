<?php

namespace Chernoff\WHM\ValidationRules;

use Chernoff\WHM\Interfaces\ValidationRuleInterface;

/**
 * Class AccountNotFound
 * @package Chernoff\WHM\ValidationRules
 */
class AccountNotFound implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws \Exception
     */
    public function validate($response)
    {
        if (empty($response->acct[0])) {
            throw new \Exception("Account not found on frontend.");
        }
    }
}
