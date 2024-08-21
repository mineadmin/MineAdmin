/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
export default defineComponent({
  name: 'notification',
  setup() {
    const selected = ref<string>('message')
    return () => (
      <div class="hidden lg:block">
        <m-dropdown
          class="max-w-[300px] min-w-[300px]"
          triggers={['click']}
          style="position: relative; top: 3px"
          v-slots={{
            default: () => (
              <div class="relative">
                <ma-svg-icon
                  className="tool-icon"
                  name="heroicons:bell"
                  size={20}
                />
                <div class="absolute right-0 top-0 h-2 flex items-center rounded-full bg-red-5 px-1 text-[9px]" />
              </div>
            ),
            popper: () => (
              <div>
                <m-tabs
                  v-model={selected.value}
                  options={[
                    { icon: 'i-ph:chat-circle-text', label: useTrans('mineAdmin.notification.message'), value: 'message' },
                    { icon: 'i-ic:baseline-notifications-none', label: useTrans('mineAdmin.notification.notice'), value: 'notice' },
                    { icon: 'i-pajamas:todo-done', label: useTrans('mineAdmin.notification.todo'), value: 'todo' },
                  ]}
                />
                <div class="notification-box">
                  {selected.value === 'message' && (
                    <ul class="message-box">
                      {Array.from({ length: 10 }).map(_ => (
                        <li>
                          <div class="w-2/12 flex items-start justify-center">
                            <img src="https://demo.mineadmin.com/upload/uploadfile/20230330/499990370432749568.jpg" class="h-8 w-8 rounded-full" />
                          </div>
                          <div class="w-10/12">
                            <div>
                              <span class="mr-0.5 text-[rgb(var(--ui-primary))]">菜虚坤</span>
                              发来了消息
                            </div>
                            <div class="mt-1 truncate text-gray-5 dark-text-gray-4">
                              你好，我是练习时长两年半的牛马，想邀请你一起写个项目，你看如何？
                            </div>
                          </div>
                        </li>
                      ))}
                    </ul>
                  )}
                  {selected.value === 'notice' && (
                    <ul class="notice-box">
                      <li>
                        <div class="flex items-center justify-between">
                          <span class="w-8/12 truncate">人事部：关于薪资调整的通知</span>
                          <span class="text-gray-5">2024-08-01</span>
                        </div>
                        <div>
                          <div class="mt-2 truncate text-gray-5 dark-text-gray-4">
                            统统降低10%，谁赞成，谁反对？
                          </div>
                        </div>
                      </li>
                    </ul>
                  )}
                  {selected.value === 'todo' && (
                    <ul class="todo-box">
                      <li>
                        <div class="flex items-center justify-between">
                          <span class="w-9/12 truncate">有重要的bug要修复。</span>
                          <span class="block rounded bg-blue-1 p-1 px-2 text-[12px] text-blue-5">进行中</span>
                        </div>
                        <div class="mt-2 truncate text-gray-5 dark-text-gray-4">请免费加班处理，加班费没有，可调休。</div>
                      </li>
                      <li>
                        <div class="flex items-center justify-between">
                          <span class="w-9/12 truncate">新功能开发</span>
                          <span class="block rounded bg-gray-1 p-1 px-2 text-[12px] text-gray-5">未开始</span>
                        </div>
                        <div class="mt-2 truncate text-gray-5 dark-text-gray-4">多租户功能开发，请在1天内完成。</div>
                      </li>
                      <li>
                        <div class="flex items-center justify-between">
                          <span class="w-9/12 truncate">框架功能重构</span>
                          <span class="block rounded bg-red-1 p-1 px-2 text-[12px] text-red-5">快到期</span>
                        </div>
                        <div class="mt-2 truncate text-gray-5 dark-text-gray-4">譬如朝露，去日苦多，重构吧。</div>
                      </li>
                    </ul>
                  )}
                </div>
                <div class="box-footer">
                  <a class="link">{useTrans('mineAdmin.notification.allRead')}</a>
                  <a class="link">{useTrans('mineAdmin.notification.gotoTheList')}</a>
                </div>
              </div>
            ),
          }}
        />
      </div>
    )
  },
})
