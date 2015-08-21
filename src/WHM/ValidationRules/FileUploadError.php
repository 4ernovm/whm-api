<?php

namespace Chernoff\WHM\ValidationRules;

use Chernoff\WHM\Interfaces\ValidationRuleInterface;
use Exception;

/**
 * Class FileUploadError
 * @package Chernoff\WHM\ValidationRules
 */
class FileUploadError implements ValidationRuleInterface
{
    /**
     * @param $response
     * @return bool|void
     * @throws Exception
     */
    public function validate($response)
    {
        if (empty($response->status)) {
            throw new Exception($response->errors[0]);
        }
    }
}
