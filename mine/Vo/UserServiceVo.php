<?php

namespace Mine\Vo;

class UserServiceVo
{
    /**
     * 用户名
     * @var string
     */
    protected string $username;

    /**
     * 密码
     * @var string
     */
    protected string $password;

    /**
     * 手机
     */
    protected string $phone;

    /**
     * 邮箱
     */
    protected string $email;

    /**
     * 验证码
     * @var string
     */
    protected string $verifyCode;

    /**
     * 其他数据
     * @var array
     */
    protected array $other;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getVerifyCode(): string
    {
        return $this->verifyCode;
    }

    /**
     * @param string $verifyCode
     */
    public function setVerifyCode(string $verifyCode): void
    {
        $this->verifyCode = $verifyCode;
    }

    /**
     * @return array
     */
    public function getOther(): array
    {
        return $this->other;
    }

    /**
     * @param array $other
     */
    public function setOther(array $other): void
    {
        $this->other = $other;
    }
}