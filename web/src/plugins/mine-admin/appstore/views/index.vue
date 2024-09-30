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
</script>

<template>
  <div class="mine-card">
    {{ t('appstore') }}

    <ma-upload-image :size="120" multiple>
      <template #tip>
        请上传2M以内的图片
      </template>
    </ma-upload-image>
  </div>
</template>

<style scoped lang="scss">

</style>
