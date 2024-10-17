<i18n lang="yaml">
en:
  home: Back to Home
zh_CN:
  home: 回到首页
zh_TW:
  home: 回到首頁
</i18n>

<script setup lang="ts">
import image404 from '@/assets/images/404.svg'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

const t = useLocalTrans()

const router = useRouter()
</script>

<template>
  <div class="w-full flex flex-col-center">
    <img :src="image404" width="600" alt="404">
    <div class="flex">
      <m-button @click="() => router.replace('/')">
        <ma-svg-icon name="i-material-symbols:home-outline-rounded" :size="20" />
        {{ t('home') }}
      </m-button>
    </div>
  </div>
</template>
