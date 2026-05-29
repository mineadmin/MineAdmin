<?php

namespace App\Http\Common\Request;

use Illuminate\Http\Request;

/**
 * @mixin Request
 */
trait ClientOsTrait
{
    public function os(): string
    {
        $userAgent = $this->header('user-agent');
        if (empty($userAgent)) {
            return 'Unknown';
        }

        return match (true) {
            preg_match('/win/i', $userAgent) => 'Windows',
            preg_match('/mac/i', $userAgent) => 'MAC',
            preg_match('/linux/i', $userAgent) => 'Linux',
            preg_match('/unix/i', $userAgent) => 'Unix',
            preg_match('/bsd/i', $userAgent) => 'BSD',
            default => 'Other',
        };
    }
}
