<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import { OverlayScrollbarsComponent } from 'overlayscrollbars-vue'
import { useI18n } from 'vue-i18n'
import UcTitle from './components/title.vue'
import UcContainer from './components/container.vue'
import useDayjs from '@/hooks/auto-imports/useDayjs.ts'

const { t } = useI18n()
const selected = ref('message')
const state = reactive({
  selectedMessage: 1,
  selectedNotice: 2,
  selectedTodo: 3,
})
const tabOptions = reactive([
  { icon: 'i-ph:chat-circle-text', label: useTrans('mineAdmin.notification.message'), value: 'message' },
  { icon: 'i-ic:baseline-notifications-none', label: useTrans('mineAdmin.notification.notice'), value: 'notice' },
  { icon: 'i-pajamas:todo-done', label: useTrans('mineAdmin.notification.todo'), value: 'todo' },
])
</script>

<template>
  <UcContainer>
    <UcTitle>
      <template #extra>
        <m-button class="h-8">
          {{ t('mineAdmin.uc.setAllRead') }}
        </m-button>
      </template>
    </UcTitle>
    <div class="mine-uc-layout-content">
      <OverlayScrollbarsComponent
        class="slider"
        :options="{ scrollbars: { visibility: 'hidden' } }"
      >
        <m-tabs
          v-model="selected"
          class="z-10 text-sm !sticky !top-0 !rounded-none"
          :options="tabOptions"
        />

        <ul class="message-list">
          <template
            v-for="(item, idx) in Array.from({ length: 20 })"
          >
            <li class="group" :class="{ active: idx === state.selectedMessage }" @click="() => state.selectedMessage = idx">
              <div class="msg-title">
                你的 MineAdmin 账号在 Windows NT 10.0 的 Chrome 127.0.0.1 上登录
              </div>
              <div class="other-info">
                <span>{{ useDayjs(`2024-08-05 21:20:31`).fromNow() }}</span>
                <span class="right hidden group-hover-flex">
                  <a class="set-read">设为已读</a>
                  <a class="delete">删除</a>
                </span>
              </div>
            </li>
          </template>
        </ul>
      </OverlayScrollbarsComponent>
      <div
        class="message-content"
        v-html="
          `消息内容 - ${state.selectedMessage}
<div>
    <p>你的 MineAdmin 账号在 Windows NT 10.0 的 Chrome 127.0.0.1 上登录</p>
    <p>测试消息内容。。。。。。。。。。。</p>
    <p>测试消息内容。。。。。。。。。。。</p>
    <p>测试消息内容。。。。。。。。。。。</p>
    <p>测试消息内容。。。。。。。。。。。</p>
    <p>测试消息内容。。。。。。。。。。。</p>
</div>
`
        "
      />
    </div>
  </UcContainer>
</template>

<style scoped lang="scss">
.slider {
  @apply w-full lg:w-3/12 b-r-0 lg:b-r-1
    b-r-gray-2 b-r-solid pr-2.5 dark-b-dark-5 h-[10rem]
  lg:h-full overflow-y-auto relative
  ;
}

.message-list {
  li {
    list-style: none;

    @apply p-2 py-3 text-sm cursor-pointer flex flex-col gap-y-3
      b-b-1 b-b-solid b-b-stone-2 dark-b-b-dark-5 b-l-2 b-l-solid b-l-white dark-b-l-dark-8 ;

    .msg-title {
      @apply truncate;
    }

    .msg-title.unread {
      @apply font-bold;
    }

    .other-info {
      @apply flex justify-between text-[12px] items-center text-gray-5 dark-text-gray-4
      ;

      .right {
        @apply gap-x-3 text-sm;

        .set-read {
          @apply hover-text-dark-8 dark-hover-text-gray-1;
        }

        .delete {
          @apply text-red-6;
        }
      }
    }
  }

  li.active {
    @apply b-l-green-6 bg-stone-50 dark-bg-dark-6
     ;
  }
}

.message-content {
  @apply w-[calc(100%-20px)] lg:w-9/12 text-sm p-2.5 leading-7 mt-3 lg:mt-0
    b-t-1 lg:b-t-0 b-t-gray-2 b-t-solid dark-b-t-dark-5
  ;
}
</style>
