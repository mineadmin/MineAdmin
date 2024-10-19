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
  </div>
</template>

<style scoped lang="scss">

</style>
