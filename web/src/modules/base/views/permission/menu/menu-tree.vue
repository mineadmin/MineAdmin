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
import type { MenuVo } from '~/base/api/menu.ts'
import { deleteByIds } from '~/base/api/menu.ts'
import { useResizeObserver } from '@vueuse/core'
import getOnlyWorkAreaHeight from '@/utils/getOnlyWorkAreaHeight.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import type { Ref } from 'vue'

const emit = defineEmits<{
  (event: 'menu-select', value: MenuVo): void
}>()

const maTreeRef = ref()
const msg = useMessage()
const newMenu = inject('newMenu') as Ref<MenuVo>
const setNodeExpand = inject('setNodeExpand') as (id: number, state: boolean) => void

const t = useTrans().globalTrans

const menuType = ref<{ [key: string]: Record<string, string> }>({
  M: { color: 'primary', label: '菜单' },
  B: { color: 'danger', label: '按钮' },
  L: { color: 'success', label: '外链' },
  I: { color: 'warning', label: 'iFrame' },
})

function addMenu(data: MenuVo) {
  const newData: MenuVo = newMenu.value
  newData.dataType = 'add'
  newData.id = undefined
  newData.parent_id = data.id
  newData.meta!.title = '新菜单'
  emit('menu-select', newData)
}

defineExpose({
  treeRef: maTreeRef,
})

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
    ref="maTreeRef"
    tree-key="meta.title"
    highlight-current
    :expand-on-click-node="false"
    node-key="id"
    :indent="26"
    auto-expand-parent
    class="mt-1 h-[200px] lg:h-[calc(100%-80px)]"
    @node-click="(node: MenuVo) => {
      emit('menu-select', node)
    }"
  >
    <template #default="{ node, data }">
      <div class="mine-tree-node">
        <div class="label">
          <ma-svg-icon v-if="data.meta?.icon" :name="data.meta?.icon" :size="20" />
          {{ data.meta?.i18n ? t(data.meta?.i18n) : data.meta.title ?? 'unknown' }}
        </div>
        <div class="do" :class="{ '!inline-block': maTreeRef.elTree.getCurrentKey() === data.id }">
          <el-tag :type="menuType[data.meta?.type].color as any" class="mr-2">
            {{ menuType[data.meta?.type].label }}
          </el-tag>
          <el-button
            v-if="data.meta?.type === 'M'"
            v-auth="['permission:menu:save']"
            size="small" circle type="primary"
            @click.stop="addMenu(data)"
          >
            <ma-svg-icon name="ic:round-plus" :size="20" />
          </el-button>
          <el-popconfirm
            :title="t('crud.delDataMessage')"
            :confirm-button-text="t('crud.ok')"
            :cancel-button-text="t('crud.cancel')"
            @confirm.stop="async () => {
              if (data.children && data.children.length > 0) {
                msg.notifyError(t('baseMenuManage.deleteChildren'))
                return
              }
              if (data.parent_id !== 0) {
                setNodeExpand(data.parent_id, true)
                emit('menu-select', maTreeRef.elTree.getCurrentNode())
              }
              await deleteByIds([data.id])
              maTreeRef.elTree.remove(node)
            }"
          >
            <template #reference>
              <el-button
                v-auth="['permission:menu:delete']" size="small" circle type="danger"
              >
                <ma-svg-icon name="ic:round-minus" :size="20" />
              </el-button>
            </template>
          </el-popconfirm>
        </div>
      </div>
    </template>
    <template #extra>
      <el-button
        type="primary"
        class="w-full"
        @click="() => emit('menu-select', newMenu)"
      >
        {{ t('baseMenuManage.addTopMenu') }}
      </el-button>
    </template>
  </ma-tree>
</template>

<style scoped lang="scss">

</style>
