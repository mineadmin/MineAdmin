<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->

<i18n lang="yaml">
zh_CN:
  clearLogs: 清空日志
zh_TW:
  clearLogs: 清空日志
en:
  clearLogs: Clear logs
</i18n>

<script setup lang="ts">
import { useLocalTrans } from '@/hooks/useLocalTrans'
import UcContainer from './components/container.vue'
import UcTitle from './components/title.vue'
</script>

<template>
  <UcContainer>
    <UcTitle>
      <template #extra>
        <m-button class="h-8">
          {{ useLocalTrans('clearLogs') }}
        </m-button>
      </template>
    </UcTitle>

    <div class="mine-uc-layout-content !h-auto">
      <ul class="info-list b-1 b-gray-2 rounded b-solid dark-b-dark-4">
        <li class="bg-gray-1 !b-none dark-bg-dark-5">
          <div class="desc-item">
            <div class="desc-label font-bold">
              {{ useLocalTrans('clearLogs') }}
            </div>
            <div class="desc-value font-bold">
              {{ useLocalTrans('clearLogs') }}
            </div>
          </div>
        </li>
        <li>
          <div class="desc-item">
            <div class="desc-label">
              aaa
            </div>
            <div class="desc-value">
              aa
            </div>
          </div>
        </li>
        <li>
          <div class="desc-item">
            <div class="desc-label">
              bbb
            </div>
            <div class="desc-value">
              bbb
            </div>
          </div>
        </li>
      </ul>
    </div>
  </UcContainer>
</template>

<style scoped lang="scss">
.info-list {
  @apply w-full;

  & li {
    @apply b-t-1 b-t-gray-2 b-t-solid pr-2.5 dark-b-t-dark-5 py-4 text-sm hover-bg-gray-50 dark-hover-bg-dark-5;

    .desc-item {
      @apply w-full lg:w-6/12 flex items-center justify-between text-stone-8 dark-text-stone-3;

      .desc-label {
        @apply w-6/12 lg:w-5/12 pl-2 lg:pl-3 truncate;
      }

      .desc-value {
        @apply w-6/12 lg:w-7/12 text-left truncate;
      }
    }
  }
}
</style>
