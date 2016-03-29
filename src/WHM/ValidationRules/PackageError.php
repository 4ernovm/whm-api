<?php

namespace Chernoff\WHM\ValidationRules;

use Chernoff\WHM\Interfaces\ValidationRuleInterface;

/**
 * Class PackageError
 * @package Chernoff\WHM\ValidationRules
 */
class PackageError implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws \Exception
     */
    public function validate($response)
    {
        if (empty($response) || empty($response->data->pkg)) {
            throw new \Exception("Package error");
        }
    }
}
