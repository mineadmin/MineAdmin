<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Helper;

class MineCaptcha
{
    /**
     * @return array
     */
    public function getCaptchaInfo(): array
    {
        $conf = new \EasySwoole\VerifyCode\Config();
        $conf->setUseCurve()->setUseNoise();
        $validCode = new \EasySwoole\VerifyCode\VerifyCode($conf);
        $draw = $validCode->DrawCode();
        return ['code' => \Mine\Helper\Str::lower($draw->getImageCode()), 'image' => $draw->getImageByte()];
    }
}
