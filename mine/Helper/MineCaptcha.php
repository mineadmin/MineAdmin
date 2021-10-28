<?php
declare(strict_types=1);
namespace Mine\Helper;

class MineCaptcha
{
    /**
     * @return array
     */
    public function getCaptchaInfo(): array
    {
        $conf = new \EasySwoole\VerifyCode\Conf();
        $conf->setUseCurve()->setUseNoise();
        $validCode = new \EasySwoole\VerifyCode\VerifyCode($conf);
        $draw = $validCode->DrawCode();
        return ['code' => \Mine\Helper\Str::lower($draw->getImageCode()), 'image' => $draw->getImageByte()];
    }
}