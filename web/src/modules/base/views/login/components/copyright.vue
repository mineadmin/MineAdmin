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
import { useI18n } from 'vue-i18n'
import type { SystemSettings } from '#/global'

const { locale } = useI18n()
const userStore = useUserStore()
const settingStore = useSettingStore()

function switchLanguage(language: string): void {
  locale.value = language
  userStore.setLanguage(language)
}

const title = import.meta.env.VITE_APP_TITLE

const setting: SystemSettings.copyright = settingStore.getSettings('copyright')
</script>

<template>
  <div class="absolute bottom-5 z-30 w-[335px] text-center text-sm text-gray-3 lg:text-gray-5">
    <div class="mb-1 flex justify-center gap-x-2 text-sm">
      <a v-for="item in userStore.getLocales()" class="trans-link" @click="() => switchLanguage(item.value)">
        {{ item.label }}
      </a>
    </div>
    <div class="flex justify-center gap-x-2.5">
      <p class="flex items-center justify-center text-sm">
        <ma-svg-icon name="lucide:copyright" />
      </p>
      <span>{{ `${new Date().getFullYear()} ${title}` }}</span>
      <span><a href="https://beian.miit.gov.cn/" target="_blank" class="trans-link">{{ setting.putOnRecord }}</a></span>
    </div>
  </div>
</template>

<style scoped lang="scss">
.trans-link {
  @apply text-gray-3 lg:text-gray-7 lg:hover-text-gray-9 cursor-pointer decoration-none;
}
</style>
