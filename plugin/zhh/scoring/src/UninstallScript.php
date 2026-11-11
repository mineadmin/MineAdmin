<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 插件卸載腳本（方案 §4.2）：僅回滾本插件創建的 7 張計分表；用戶中心表由 zhh/user-center 自行卸載。
 */

namespace Plugin\Zhh\Scoring;

use App\Model\Permission\Menu;
use Hyperf\Context\ApplicationContext;
use Hyperf\Database\Schema\Schema;
use Hyperf\Logger\LoggerFactory;
use Throwable;

class UninstallScript
{
    /**
     * 本插件在 menu 表註冊的菜單與權限碼（與 Database/Seeders/MenuSeeder.php 保持一致）
     */
    private const MENU_NAMES = [
        'scoring',
        'scoring:room',
        'scoring:room:index',
    ];

    /**
     * MineAdmin 文檔（插件生命週期）約定的卸載入口，返回 true 才視為卸載成功。
     *
     * 卸載順序：先子表後主表，避免殘留引用；此處刻意逐條書寫而不寫循環
     * （方案 §1.3 硬性規範：數據庫操作不得出現在循環體內）。
     */
    public function handle(): bool
    {
        try {
            $this->cleanMenuPermissions();
            Schema::dropIfExists('scoring_settle_transfer');
            Schema::dropIfExists('scoring_battle_member');
            Schema::dropIfExists('scoring_room_event');
            Schema::dropIfExists('scoring_record');
            Schema::dropIfExists('scoring_battle');
            Schema::dropIfExists('scoring_room_member');
            Schema::dropIfExists('scoring_room');

            echo "zhh/scoring 插件卸載完成\n";
            return true;
        } catch (Throwable $e) {
            ApplicationContext::getContainer()
                ->get(LoggerFactory::class)
                ->get('error')
                ->error('卸載 zhh/scoring 失敗：' . $e->getMessage(), ['exception' => $e]);

            return false;
        }
    }

    /**
     * 保留 mine-extension:create 生成的骨架入口，避免與框架實際調用約定（handle / __invoke）不一致導致腳本不被執行。
     */
    public function __invoke(): bool
    {
        return $this->handle();
    }

    /**
     * 清理菜單與權限碼（方案 §8.3）：一次查出 id 後交給 Model::destroy 批量刪除，
     * 使 Menu 模型的刪除事件可以聯動清理角色綁定。
     */
    protected function cleanMenuPermissions(): void
    {
        $menuIds = Menu::getModel()->newQuery()
            ->whereIn('name', self::MENU_NAMES)
            ->pluck('id')
            ->toArray();

        if ($menuIds !== []) {
            Menu::destroy($menuIds);
        }
    }
}
