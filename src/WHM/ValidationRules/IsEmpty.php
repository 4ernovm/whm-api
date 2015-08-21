<?php

namespace WHM\ValidationRules;

use WHM\Interfaces\ValidationRuleInterface;

/**
 * Class IsEmpty
 * @package WHM\ValidationRules
 */
class IsEmpty implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws \Exception
     */
    public function validate($response)
    {
        if (empty($response) || empty($response->cpanelresult)) {
            throw new \Exception("cPanel Result NULL");
        }
    }
}
