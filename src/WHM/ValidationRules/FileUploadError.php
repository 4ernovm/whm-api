<?php

namespace WHM\ValidationRules;

use WHM\Interfaces\ValidationRuleInterface;
use Exception;

/**
 * Class FileUploadError
 * @package WHM\ValidationRules
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
