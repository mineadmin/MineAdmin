<?php
declare(strict_types=1);
namespace Mine\Helper;


use Minho\Captcha\CaptchaBuilder;

class MineCaptcha extends CaptchaBuilder
{
    /**
     * @return string
     */
    public function getBase64(): string
    {
        ob_start();
        imagepng($this->image, null, 1);
        $png = ob_get_contents();
        ob_end_clean();
        return 'data:image/png;base64,' . base64_encode($png);
    }
}