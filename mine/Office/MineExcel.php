<?php
declare(strict_types=1);

/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace Mine\Office;

use App\System\Service\SystemDictDataService;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Mine\Exception\MineException;
use Mine\Interfaces\MineModelExcel;
use Mine\MineResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class MineExcel
{
    public const ANNOTATION_NAME = 'Mine\Annotation\ExcelProperty';

    /**
     * @var array|null
     */
    protected ?array $annotationMate;

    /**
     * @var array
     */
    protected array $property = [];
    protected array $dictData = [];


    /**
     * @param String $dto
     * @param MineModel $model
     */
    public function __construct(string $dto)
    {
        if (!(new $dto) instanceof MineModelExcel) {
            throw new MineException('dto does not implement an interface of the MineModelExcel', 500);
        }
        $dtoObject = new $dto();
        if (method_exists($dtoObject, 'dictData')) {
            $this->dictData = $dtoObject->dictData();
        }
        $this->annotationMate = AnnotationCollector::get($dto);
        $this->parseProperty();
    }

    /**
     * @return array
     */
    public function getProperty(): array
    {
        return $this->property;
    }

    /**
     * @return array
     */
    public function getAnnotationInfo(): array
    {
        return $this->annotationMate;
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    protected function parseProperty(): void
    {
        if (empty($this->annotationMate) || !isset($this->annotationMate['_c'])) {
            throw new MineException('dto annotation info is empty', 500);
        }

        foreach ($this->annotationMate['_p'] as $name => $mate) {
            $this->property[$mate[self::ANNOTATION_NAME]->index] = [
                'name' => $name,
                'value' => $mate[self::ANNOTATION_NAME]->value,
                'width' => $mate[self::ANNOTATION_NAME]->width ?? null,
                'align' => $mate[self::ANNOTATION_NAME]->align ?? null,
                'headColor' => $mate[self::ANNOTATION_NAME]->headColor ?? null,
                'headBgColor' => $mate[self::ANNOTATION_NAME]->headBgColor ?? null,
                'color' => $mate[self::ANNOTATION_NAME]->color ?? null,
                'bgColor' => $mate[self::ANNOTATION_NAME]->bgColor ?? null,
                'dictData' => $mate[self::ANNOTATION_NAME]->dictData,
                'dictName' => empty($mate[self::ANNOTATION_NAME]->dictName) ? null : $this->getDictData($mate[self::ANNOTATION_NAME]->dictName),
                'path' => $mate[self::ANNOTATION_NAME]->path ?? null,
            ];
        }
        ksort($this->property);
    }

    /**
     * 下载excel
     * @param string $filename
     * @param string $content
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function downloadExcel(string $filename, string $content): \Psr\Http\Message\ResponseInterface
    {
        return container()->get(MineResponse::class)->getResponse()
            ->withHeader('Server', 'MineAdmin')
            ->withHeader('content-description', 'File Transfer')
            ->withHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('content-disposition', "attachment; filename={$filename}; filename*=UTF-8''" . rawurlencode($filename))
            ->withHeader('content-transfer-encoding', 'binary')
            ->withHeader('pragma', 'public')
            ->withBody(new SwooleStream($content));
    }

    /**
     * 获取字典数据
     * @param string $dictName
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    protected function getDictData(string $dictName): array
    {
        $data = [];
        foreach (container()->get(SystemDictDataService::class)->getList(['code' => $dictName]) as $item) {
            $data[$item['key']] = $item['title'];
        }

        return $data;
    }
}