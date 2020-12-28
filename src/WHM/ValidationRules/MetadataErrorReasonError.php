<?php

namespace Chernoff\WHM\ValidationRules;

use Chernoff\WHM\Interfaces\ValidationRuleInterface;

/**
 * Class MetadataErrorReasonError
 * @package Chernoff\WHM\ValidationRules
 */
class MetadataErrorReasonError implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws \Exception
     */
    public function validate($response)
    {
        if ($response->metadata->result != 1) {
            throw new \Exception($response->metadata->reason);
        }
    }
}
