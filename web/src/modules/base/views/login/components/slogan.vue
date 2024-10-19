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
import pkg from '@/../package.json'

const { t } = useI18n()
const appTitle = ref<string>(import.meta.env.VITE_APP_TITLE)
</script>

<template>
  <div class="absolute left-10 z-10 w-[80%] text-white">
    <p class="mt-5 text-[35px] font-bold">
      {{ appTitle }} <span class="text-sm">v{{ pkg.version }}</span>
    </p>
    <div class="slogan">
      {{ t('mineAdmin.slogan') }}
    </div>
    <div class="mt-3 flex items-center gap-x-2">
      <ma-svg-icon name="skill-icons:php-light" class="text-4xl" />
      <ma-svg-icon name="material-symbols:add" class="text-3xl op-50" />
      <ma-svg-icon name="ri:vuejs-fill" class="text-4xl" />
      <ma-svg-icon name="material-symbols:add" class="text-3xl op-50" />
      <ma-svg-icon name="ep:element-plus" class="text-4xl" />
    </div>
  </div>
</template>
