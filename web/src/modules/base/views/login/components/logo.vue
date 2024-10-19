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
import LogoSvg from '@/assets/images/logo.svg'

const { t } = useI18n()

const appTitle = ref<string>(import.meta.env.VITE_APP_TITLE)
</script>

<template>
  <div class="relative w-auto flex items-center gap-x-3">
    <img :alt="appTitle" :src="LogoSvg" class="login-logo">
    <h3 class="text-4xl text-white tracking-[3px] lg:text-[#2d2d33ff]">
      {{ appTitle }}
    </h3>
    <div class="pure-tag">
      {{ t('mineAdmin.mark') }}
    </div>
  </div>
</template>
