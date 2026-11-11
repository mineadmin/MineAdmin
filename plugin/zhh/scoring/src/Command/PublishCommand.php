<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 插件發布/更新輔助命令（方案 §4.5 已安裝插件的更新操作）。
 *
 * 背景：MineAdmin 插件系統（mineadmin/app-store）只有 install / uninstall 兩個生命週期，
 * 沒有 update 流程——官方文檔 doc.mineadmin.com/v3/plugin/api.html#update 所述的
 * Plugin::update() 為未實現的藍圖（vendor 源碼中無該方法，同頁 HTTP API / HookManager
 * 亦未落地），且 uninstall 會 drop 插件數據表、install 對已安裝插件直接拒絕。
 * 因此已安裝插件更新代碼後，需手工處理兩類「數據態」殘留，本命令將其固化為冪等操作：
 *
 *  1. 清理廢棄菜單/權限碼節點：代碼中已刪除的菜單（如 scoring:room:delete）此前已寫入
 *     服務器 menu 表，MenuSeeder 冪等只復用不刪除 → 經 Model::destroy 刪除，觸發
 *     Menu::deleting 事件 roles()->detach() 聯動清理 role_belongs_menu；
 *     節點不存在時自動跳過，可反復執行。
 *
 *  2. 同步前端發布副本：等價於 install 時的前端文件發布
 *     （vendor/mineadmin/app-store/src/Plugin.php：plugin/{name}/web/** →
 *     web/src/plugins/{name}/**，前端構建掃描的是副本目錄）。
 *
 * 執行後的手工步驟（常駐內存與前端構建，命令無法代勞）：
 *  rm -rf runtime/container 並重啟 Hyperf 進程；cd web && pnpm build。
 */

namespace Plugin\Zhh\Scoring\Command;

use App\Model\Permission\Menu;
use Hyperf\Command\Command as HyperfCommand;
use Symfony\Component\Finder\Finder;

final class PublishCommand extends HyperfCommand
{
    /**
     * 已廢棄的菜單/權限碼清單：代碼中已刪除、但已安裝環境 menu 表可能仍殘留的節點。
     * 維護約定：刪除菜單代碼（MenuSeeder 節點）時，同步把 name 加入此處。
     */
    private const DEPRECATED_MENU_NAMES = ['scoring:room:delete'];

    protected ?string $name = 'scoring:publish';

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('zhh/scoring 已安裝插件更新輔助：清理廢棄菜單節點 + 同步前端發布副本（冪等，可反復執行）');
    }

    public function handle(): void
    {
        // 未安裝環境（純開發機，無 install.lock）不碰 menu 表，僅同步前端副本
        if (file_exists($this->installLockPath())) {
            $this->cleanDeprecatedMenus();
        } else {
            $this->output->writeln('  <comment>檢測到插件未安裝（無 install.lock），跳過菜單清理</comment>');
        }

        $this->publishFrontend();

        $this->output->success('發布完成，請執行後續手工步驟：');
        $this->output->writeln([
            '  1. rm -rf runtime/container 並重啟 Hyperf 進程（常駐內存需重啟加載新代碼）',
            '  2. 前端構建：cd web && pnpm build',
        ]);
    }

    /**
     * 清理廢棄菜單節點：一次查詢 + 一次 destroy（方案 §1.3 禁止循環內操作數據庫）。
     */
    private function cleanDeprecatedMenus(): void
    {
        $menuIds = Menu::query()
            ->whereIn('name', self::DEPRECATED_MENU_NAMES)
            ->pluck('id')
            ->toArray();
        if ($menuIds === []) {
            $this->output->writeln('  <info>菜單清理：無廢棄節點，跳過</info>');
            return;
        }

        // Model::destroy 觸發 Menu::deleting（app/Model/Permission/Menu.php deleting 鉤子）
        // → roles()->detach() 聯動清理 role_belongs_menu，與後台 UI 刪除菜單行為一致
        Menu::destroy($menuIds);
        $this->output->success(sprintf(
            '菜單清理：已移除 %d 個廢棄節點（%s）',
            count($menuIds),
            implode(', ', self::DEPRECATED_MENU_NAMES)
        ));
    }

    /**
     * 同步前端發布副本：plugin/zhh/scoring/web/** → web/src/plugins/zhh/scoring/**。
     * 文件複製不屬於數據庫操作，不在「禁止循環內操作數據庫」限制範圍（方案 §1.3）。
     */
    private function publishFrontend(): void
    {
        $pluginWebPath = $this->pluginWebPath();
        if (! is_dir($pluginWebPath)) {
            $this->output->writeln('  <info>前端同步：插件無 web 目錄，跳過</info>');
            return;
        }

        $targetPath = $this->frontTargetPath();
        if (! is_dir($targetPath) && ! mkdir($targetPath, 0755, true) && ! is_dir($targetPath)) {
            throw new \RuntimeException(sprintf('無法創建前端插件目錄：%s', $targetPath));
        }

        $count = 0;
        foreach (Finder::create()->files()->in($pluginWebPath) as $file) {
            $target = $targetPath . DIRECTORY_SEPARATOR . $file->getRelativePathname();
            $dir = dirname($target);
            if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
                throw new \RuntimeException(sprintf('無法創建目錄：%s', $dir));
            }
            copy($file->getPathname(), $target);
            ++$count;
        }

        $this->output->success(sprintf(
            '前端同步：%d 個文件 → web/src/plugins/zhh/scoring',
            $count
        ));
    }

    private function installLockPath(): string
    {
        return BASE_PATH . '/plugin/zhh/scoring/install.lock';
    }

    private function pluginWebPath(): string
    {
        return BASE_PATH . '/plugin/zhh/scoring/web';
    }

    private function frontTargetPath(): string
    {
        return BASE_PATH . '/web/src/plugins/zhh/scoring';
    }
}
