<i18n lang="yaml">
zh_CN:
  appstore: 应用商店
zh_TW:
  appstore: 應用商店
en:
  appstore: App store
</i18n>

<script setup lang="ts">
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MineAppStoreRoute' })

const t = useLocalTrans() as any

const ok = ref('http://127.0.0.1:9501/uploads/2024-10-01/594a88c3-35df-4fc5-ac5b-030a2d4274d1.jpg')
</script>

<template>
  <div class="mine-card">
    {{ t('appstore') }}

    <ma-upload-image v-model="ok" :size="120" multiple />
  </div>
</template>

<style scoped lang="scss">

</style>
