<?php

namespace Mine\Event;

class UserLoginBefore
{
    public $inputData;

    public function __construct(array $inputData)
    {
        $this->inputData = $inputData;
    }
}