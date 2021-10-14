<?php


namespace Mine\Event;


use Mine\MineRequest;

class UserLoginAfter
{
    public $userinfo;

    public $loginStatus = true;

    public $message;

    public $token;

    public function __construct(array $userinfo)
    {
        $this->userinfo = $userinfo;
    }
}