<?php

namespace WHM\Interfaces;

/**
 * Interface ManageUploadInterface
 * @package WHM\Interfaces
 */
interface ManageUploadInterface
{
    /**
     * $files is an associative array where the keys are absolute paths to
     * local files and values are corresponding remote paths relative to
     * user's home folder.
     *
     * @param array $files
     * @return bool
     */
    public function upload(array $files);
}
