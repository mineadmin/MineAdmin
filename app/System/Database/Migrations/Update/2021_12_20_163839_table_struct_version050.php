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

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class TableStructVersion050 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_queue_message', function (Blueprint $table) {

            $table->index('content_type');
            $table->index('send_by');

            if (! Schema::hasColumn('system_queue_message','title')) {
                $table->addColumn('string', 'title', ['length' => 255])
                    ->comment('消息标题')
                    ->after('content_type')
                    ->nullable();
            }

            if (Schema::hasColumn('system_queue_message','content_id')) {
                $table->dropColumn(['content_id']);
            }

            if (Schema::hasColumn('system_queue_message','send_status')) {
                $table->dropColumn(['send_status']);
            }

            if (Schema::hasColumn('system_queue_message','read_status')) {
                $table->dropColumn(['read_status']);
            }

            if (Schema::hasColumn('system_queue_message','receive_by')) {
                $table->dropColumn(['receive_by']);
            }

            if (Schema::hasColumn('system_queue_message','deleted_at')) {
                $table->dropColumn(['deleted_at']);
            }
        });

        Schema::table('system_notice', function (Blueprint $table) {
            if (! Schema::hasColumn('system_notice', 'message_id')) {
                $table->addColumn('bigInteger', 'message_id')
                    ->comment('消息ID')
                    ->after('id')
                    ->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_queue_message', function (Blueprint $table) {
            $table->dropColumn(['title']);
        });
    }
}
