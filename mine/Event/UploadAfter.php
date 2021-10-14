<?php

namespace Mine\Event;

class UploadAfter
{
    public $fileInfo;

    public function __construct(array $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }
}