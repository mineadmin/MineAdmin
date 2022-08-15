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

namespace Mine\Event;

use Psr\Http\Message\ResponseInterface;

class ApiAfter
{
    protected ?array $apiData;

    protected ResponseInterface $result;

    public function __construct(?array $apiData, ResponseInterface $result)
    {
        $this->apiData = $apiData;
        $this->result = $result;
    }

    public function getApiData(): ?array
    {
        return $this->apiData;
    }

    public function getResult(): ResponseInterface
    {
        return $this->result;
    }
}