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
namespace Mine;

use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Mine\Exception\MineException;
use Mine\Interfaces\MineModelExcel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MineCollection extends Collection
{
    /**
     * 系统菜单转前端路由树
     * @return array
     */
    public function sysMenuToRouterTree(): array
    {
        $data = $this->toArray();
        if (empty($data)) return [];

        $routers = [];
        foreach ($data as $menu) {
            array_push($routers, $this->setRouter($menu));
        }
        return $this->toTree($routers);
    }

    /**
     * @param $menu
     * @return array
     */
    public function setRouter(&$menu): array
    {
        return [
            'id' => $menu['id'],
            'parent_id' => $menu['parent_id'],
            'name' => $menu['code'],
            'component' => $menu['component'],
            'path' => '/' . $menu['route'],
            'redirect' => $menu['redirect'],
            'meta' => [
                'type'   => $menu['type'],
                'icon'   => $menu['icon'],
                'title'  => $menu['name'],
                'hidden' => ($menu['is_hidden'] == 0),
                'hiddenBreadcrumb' => false
            ]
        ];
    }

    /**
     * @param array $data
     * @param int $parentId
     * @param string $id
     * @param string $parentField
     * @param string $children
     * @return array
     */
    public function toTree(array $data = [], int $parentId = 0, string $id = 'id', string $parentField = 'parent_id', string $children='children'): array
    {
        $data = $data ?: $this->toArray();

        if (empty($data)) return [];

        $tree = [];

        foreach ($data as $value) {
            if ($value[$parentField] == $parentId) {
                $child = $this->toTree($data, $value[$id], $id, $parentField, $children);
                if (!empty($child)) {
                    $value[$children] = $child;
                }
                array_push($tree, $value);
            }
        }

        unset($data);
        return $tree;
    }

    /**
     * 导出数据
     * @param string $dto
     * @param string $filename
     * @param array|\Closure|null $closure
     * @return object|\Psr\Http\Message\ResponseInterface|null
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function export(string $dto, string $filename, array|\Closure $closure = null)
    {
        $data = $this->excelDataInit($dto, $closure);
        $spread = new Spreadsheet();
        $sheet  = $spread->getActiveSheet();
        $filename .= date('Y-m-d_H_i_s'). '.xlsx';

        // 表头
        $titleStart = 'A';
        foreach ($data['field'] as $item) {
            $sheet->setCellValue($titleStart . '1', $item['value']);
            $sheet->getStyle($titleStart . '1')->getFont()->setBold(true);
            $titleStart++;
        }

        $generate = $this->yieldExcelData($data['data'], $data['field']);
        
        // 表体
        try {
            $row = 2;
            while ($generate->valid()) {
                $column = 'A';
                foreach ($generate->current() as $value) {
                    $sheet->setCellValue($column . $row, (string) $value . "\t");
                    $column++;
                }
                $generate->next();
                $row++;
            }
        } catch (\RuntimeException $e) {}

        $response = container()->get(MineResponse::class);
        $writer = IOFactory::createWriter($spread, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        $res = $response->getResponse()
            ->withHeader('Server', 'MineAdmin')
            ->withHeader('content-description', 'File Transfer')
            ->withHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('content-disposition', "attachment; filename=".rawurlencode($filename))
            ->withHeader('content-transfer-encoding', 'binary')
            ->withHeader('pragma', 'public')
            ->withBody(new SwooleStream(ob_get_contents()));
        ob_end_clean();
        $spread->disconnectWorksheets();
        
        return $res;
    }

    /**
     * 数据导入
     * @param string $dto
     * @param \Mine\MineModel $model
     * @param \Closure|null $closure
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function import(string $dto, MineModel $model, ?\Closure $closure = null): bool
    {
        $annMate = AnnotationCollector::get($dto);
        $annName = 'Mine\Annotation\ExcelProperty';

        if (! (new $dto) instanceof MineModelExcel) {
            throw new \RuntimeException();
        }

        if (!isset($annMate['_c'])) {
            throw new \RuntimeException();
        }

        $property = &$annMate['_p'];

        $fields = [];

        foreach ($property as $name => $item) {
            $fields[ $item[$annName]->index ] = [
                'name'  => $name,
                'value' => $item[$annName]->value
            ];
        }
        ksort($fields);

        $request = container()->get(MineRequest::class);
        $data = [];
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $tempFileName = 'temp_'.time().'.'.$file->getExtension();
            $tempFilePath = BASE_PATH . '/runtime/'. $tempFileName;
            file_put_contents($tempFilePath, $file->getStream()->getContents());
            $reader = IOFactory::createReader(IOFactory::identify($tempFilePath));
            $reader->setReadDataOnly(true);
            $sheet = $reader->load($tempFilePath);
            try {
                foreach ($sheet->getActiveSheet()->getRowIterator(2) as $k => $row) {
                    $temp = [];
                    foreach ($row->getCellIterator() as $index => $item) {
                        if (isset($fields[ (ord($index) - 65 ) ])) {
                            $temp[$fields[(ord($index) - 65)]['name']] = $item->getFormattedValue();
                        }
                    }
                    if (! empty($temp)) {
                        $data[] = $temp;
                    }
                }
                unlink($tempFilePath);
            } catch (\Throwable $e) {
                unlink($tempFilePath);
                throw new MineException($e->getMessage());
            }
        } else {
            return false;
        }

        if ($closure instanceof \Closure) {
            return $closure($model, $data);
        }

        foreach ($data as $datum) {
            $model::create($datum);
        }

        return true;
    }

    /**
     * excel 数据导出初始化
     * @param string $dto
     * @param array|Closure|null $closure
     * @return array
     */
    protected function excelDataInit(string $dto, array|\Closure $closure = null): array
    {
        $annMate = AnnotationCollector::get($dto);
        $annName = 'Mine\Annotation\ExcelProperty';

        if (! (new $dto) instanceof MineModelExcel) {
            throw new \RuntimeException();
        }

        if (!isset($annMate['_c'])) {
            throw new \RuntimeException();
        }

        if ($closure instanceof \Closure) {
            $data = $closure();
        } else if (is_null($closure)) {
            $data = $this->toArray();
        } else if (is_array($closure)) {
            $data = &$closure;
        } else {
            $data = [];
        }

        $property = &$annMate['_p'];

        $fields = [];

        foreach ($property as $name => $item) {
            $fields[ $item[$annName]->index ] = [
                'name'  => $name,
                'value' => $item[$annName]->value
            ];
        }
        ksort($fields);
        return ['field' => &$fields, 'data' => &$data];
    }

    private function yieldExcelData(array &$data, array &$field): \Generator
    {
        foreach ($data as $dat) {
            $yield = [];
            foreach ($field as $item) {
                $yield[ $item['name'] ] = $dat[$item['name']] ?? '';
            }
            yield $yield;
        }
    }
}