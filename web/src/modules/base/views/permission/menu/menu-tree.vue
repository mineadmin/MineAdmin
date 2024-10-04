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
import { useResizeObserver } from '@vueuse/core'
import getOnlyWorkAreaHeight from '@/utils/getOnlyWorkAreaHeight.ts'
import type { MenuVo } from '~/base/api/menu.ts'

const emit = defineEmits<{
  (event: 'menu-select', value: MenuVo): void
}>()
const t = useTrans().globalTrans

onMounted(async () => {
  const resizeContainer = () => {
    const el = document.querySelector('.menu-container') as HTMLElement
    if (el) {
      el.style.height = `${getOnlyWorkAreaHeight()}px`
    }
  }
  useResizeObserver(document.querySelector('.mine-main') as HTMLElement, resizeContainer)
})
</script>

<template>
  <ma-tree
    v-bind="$attrs"
    tree-key="meta.title"
    highlight-current
    :expand-on-click-node="false"
    node-key="id"
    :indent="26"
    auto-expand-parent
    class="mt-1 h-[200px] lg:h-full"
    @node-click="(node: MenuVo) => emit('menu-select', node)"
  >
    <template #default="{ data }">
      <div class="mine-tree-node">
        <div class="label">
          <ma-svg-icon v-if="data.meta?.icon" :name="data.meta?.icon" :size="20" />
          {{ data.meta?.i18n ? t(data.meta?.i18n) : data.meta.title ?? 'unknown' }}
        </div>
        <div class="do">
          <el-button v-if="data.meta?.type === 'M'" size="small" circle type="primary">
            <ma-svg-icon name="ic:round-plus" :size="20" />
          </el-button>
          <el-button size="small" circle type="danger">
            <ma-svg-icon name="ic:round-minus" :size="20" />
          </el-button>
        </div>
      </div>
    </template>
  </ma-tree>
</template>

<style scoped lang="scss">

</style>
