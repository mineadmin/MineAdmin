<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 房間表（方案 §5.2 scoring_room）。
 */

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateScoringRoomTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scoring_room', static function (Blueprint $table) {
            $table->comment('計分房間表');
            $table->bigIncrements('id')->comment('房間ID,主鍵');
            $table->string('room_no', 16)->unique()->comment('房號（6~8位字母數字，對外展示）');
            $table->bigInteger('owner_id')->comment('房主（member_user.id）');
            $table->tinyInteger('status')->default(0)->comment('狀態:0=等待中,1=進行中,2=已結算');
            $table->tinyInteger('member_limit')->default(8)->comment('人數上限');
            $table->integer('current_round')->default(0)->comment('當前輪次');
            $table->bigInteger('current_battle_id')->nullable()->comment('當前進行中的對局ID');
            $table->integer('score_version')->default(0)->comment('分數版本號（WebSocket 推送+斷線重連校準）');
            $table->timestamp('finished_at')->nullable()->comment('結束時間');
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_room');
    }
}
