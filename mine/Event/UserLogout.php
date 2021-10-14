<?php

namespace Mine\Event;


class UserLogout
{
    public $userinfo;

    public function __construct(array $userinfo)
    {
        $this->userinfo = $userinfo;
    }
}