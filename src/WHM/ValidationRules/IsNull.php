<?php

namespace WHM\ValidationRules;

use WHM\Interfaces\ValidationRuleInterface;

/**
 * Class IsNull
 * @package WHM\ValidationRules
 */
class IsNull implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws \Exception
     */
    public function validate($response)
    {
        if (is_null($response)) {
            throw new \Exception("cPanel Result NULL");
        }
    }
}
