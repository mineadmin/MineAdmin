<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Mine\Support\Request;

use Hyperf\HttpServer\Request;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * @mixin Request
 */
trait ClientIpRequestTrait
{
    /**
     * @var string[]
     */
    protected static array $trustedProxies = [];

    private static int $trustedHeaderSet = -1;

    private array $trustedValuesCache = [];

    private bool $isForwardedValid = true;

    /**
     * Returns the client IP addresses.
     *
     * In the returned array the most trusted IP address is first, and the
     * least trusted one last. The "real" client IP address is the last one,
     * but this is also the least trusted one. Trusted proxies are stripped.
     *
     * Use this method carefully; you should use getClientIp() instead.
     *
     * @see getClientIp()
     */
    public function getClientIps(): array
    {
        $ip = $this->server('remote_addr');

        if (! $this->isFromTrustedProxy()) {
            return [$ip];
        }

        return $this->getTrustedValues(ClientIpRequestConstant::HEADER_X_FORWARDED_FOR, $ip) ?: [$ip];
    }

    /**
     * Indicates whether this request originated from a trusted proxy.
     *
     * This can be useful to determine whether or not to trust the
     * contents of a proxy-specific header.
     */
    public function isFromTrustedProxy(): bool
    {
        return self::$trustedProxies && IpUtils::checkIp($this->server('REMOTE_ADDR', ''), self::$trustedProxies);
    }

    /**
     * This method is rather heavy because it splits and merges headers, and it's called by many other methods such as
     * getPort(), isSecure(), getHost(), getClientIps(), getBaseUrl() etc. Thus, we try to cache the results for
     * best performance.
     */
    private function getTrustedValues(int $type, ?string $ip = null): array
    {
        $cacheKey = $type . "\0" . ((self::$trustedHeaderSet & $type) ? $this->header(ClientIpRequestConstant::TRUSTED_HEADERS[$type]) : '');
        $cacheKey .= "\0" . $ip . "\0" . $this->header(ClientIpRequestConstant::TRUSTED_HEADERS[ClientIpRequestConstant::HEADER_FORWARDED]);

        if (isset($this->trustedValuesCache[$cacheKey])) {
            return $this->trustedValuesCache[$cacheKey];
        }

        $clientValues = [];
        $forwardedValues = [];

        if ((self::$trustedHeaderSet & $type) && $this->hasHeader(ClientIpRequestConstant::TRUSTED_HEADERS[$type])) {
            foreach (explode(',', $this->header(ClientIpRequestConstant::TRUSTED_HEADERS[$type])) as $v) {
                $clientValues[] = ($type === ClientIpRequestConstant::HEADER_X_FORWARDED_PORT ? '0.0.0.0:' : '') . trim($v);
            }
        }

        if ((self::$trustedHeaderSet & ClientIpRequestConstant::HEADER_FORWARDED) && (isset(ClientIpRequestConstant::FORWARDED_PARAMS[$type])) && $this->hasHeader(ClientIpRequestConstant::TRUSTED_HEADERS[ClientIpRequestConstant::HEADER_FORWARDED])) {
            $forwarded = $this->header(ClientIpRequestConstant::TRUSTED_HEADERS[ClientIpRequestConstant::HEADER_FORWARDED]);
            $parts = HeaderUtils::split($forwarded, ',;=');
            $param = ClientIpRequestConstant::FORWARDED_PARAMS[$type];
            foreach ($parts as $subParts) {
                if (null === $v = HeaderUtils::combine($subParts)[$param] ?? null) {
                    continue;
                }
                if ($type === ClientIpRequestConstant::HEADER_X_FORWARDED_PORT) {
                    if (str_ends_with($v, ']') || false === $v = mb_strrchr($v, ':')) {
                        $v = $this->isSecure() ? ':443' : ':80';
                    }
                    $v = '0.0.0.0' . $v;
                }
                $forwardedValues[] = $v;
            }
        }

        if ($ip !== null) {
            $clientValues = $this->normalizeAndFilterClientIps($clientValues, $ip);
            $forwardedValues = $this->normalizeAndFilterClientIps($forwardedValues, $ip);
        }

        if ($forwardedValues === $clientValues || ! $clientValues) {
            return $this->trustedValuesCache[$cacheKey] = $forwardedValues;
        }

        if (! $forwardedValues) {
            return $this->trustedValuesCache[$cacheKey] = $clientValues;
        }

        if (! $this->isForwardedValid) {
            return $this->trustedValuesCache[$cacheKey] = $ip !== null ? ['0.0.0.0', $ip] : [];
        }
        $this->isForwardedValid = false;

        throw new \RuntimeException(\sprintf('The request has both a trusted "%s" header and a trusted "%s" header, conflicting with each other. You should either configure your proxy to remove one of them, or configure your project to distrust the offending one.', ClientIpRequestConstant::TRUSTED_HEADERS[ClientIpRequestConstant::HEADER_FORWARDED], ClientIpRequestConstant::TRUSTED_HEADERS[$type]));
    }

    private function normalizeAndFilterClientIps(array $clientIps, string $ip): array
    {
        if (! $clientIps) {
            return [];
        }
        $clientIps[] = $ip; // Complete the IP chain with the IP the request actually came from
        $firstTrustedIp = null;

        foreach ($clientIps as $key => $clientIp) {
            if (mb_strpos($clientIp, '.')) {
                // Strip :port from IPv4 addresses. This is allowed in Forwarded
                // and may occur in X-Forwarded-For.
                $i = mb_strpos($clientIp, ':');
                if ($i) {
                    $clientIps[$key] = $clientIp = mb_substr($clientIp, 0, $i);
                }
            } elseif (str_starts_with($clientIp, '[')) {
                // Strip brackets and :port from IPv6 addresses.
                $i = mb_strpos($clientIp, ']', 1);
                $clientIps[$key] = $clientIp = mb_substr($clientIp, 1, $i - 1);
            }

            if (! filter_var($clientIp, \FILTER_VALIDATE_IP)) {
                unset($clientIps[$key]);

                continue;
            }

            if (IpUtils::checkIp($clientIp, self::$trustedProxies)) {
                unset($clientIps[$key]);

                // Fallback to this when the client IP falls into the range of trusted proxies
                $firstTrustedIp ??= $clientIp;
            }
        }

        // Now the IP chain contains only untrusted proxies and the client IP
        return $clientIps ? array_reverse($clientIps) : [$firstTrustedIp];
    }
}
