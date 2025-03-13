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
en:
  placeholder: Search Label
  open: Open
  fold: Fold
  strictlyMode: Strictly mode
  strictlyModeTip: Whether or not to follow a parent-child is not related to each other
  selectAll: Select All
  invert: Invert
zh_CN:
  placeholder: 搜索名称
  open: 展开
  fold: 折叠
  strictlyMode: 严格模式
  strictlyModeTip: 是否遵循父子不互相关联
  selectAll: 全选
  invert: 反选
zh_TW:
  placeholder: 檢索名稱
  open: 展開
  fold: 收起
  strictlyMode: 嚴格模式
  strictlyModeTip: 是否遵循父子不互相关聯
  selectAll: 全選
  invert: 反選
</i18n>

<script setup lang="ts">
import { ElTree } from 'element-plus'
import { get } from 'lodash-es'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MaTree' })

const { treeKey = 'label' } = defineProps<{
  treeKey?: string
}>()

const t = useLocalTrans()

const filterText = ref<string>('')
const treeRef = ref<InstanceType<typeof ElTree>>()
const isExpand = ref<boolean>(false)
const checkStrictly = ref<boolean>(false)

watch(() => filterText.value, (val) => {
  treeRef.value!.filter(val)
})

function filterNode(value: string, data: Record<string, any>) {
  if (!value) {
    return true
  }
  return get(data, treeKey)!.includes(value)
}

function toggle() {
  isExpand.value = !isExpand.value
  const nodes = treeRef.value!.store?._getAllNodes()
  nodes.forEach((item) => {
    item.expanded = isExpand.value
  })
}

function handleSelectAll(value: boolean) {
  if (value) {
    treeRef.value?.store._getAllNodes()?.map((item) => {
      item.checked = true
    })
  }
  else {
    treeRef.value?.setCheckedKeys([])
  }
}

function handleInvert() {
  treeRef.value?.store._getAllNodes()?.map((item) => {
    item.checked = !item.checked
  })
}

function setCheckStrictly(mode: boolean) {
  checkStrictly.value = mode
}

defineExpose({
  toggle,
  handleSelectAll,
  handleInvert,
  setCheckStrictly,
  elTree: treeRef,
})
</script>

<template>
  <div class="sticky w-full">
    <div v-if="$attrs?.showCheckbox ?? false" class="flex items-center">
      <el-checkbox v-model="checkStrictly" @change="v => checkStrictly = v as boolean">
        <div class="flex items-center gap-x-1">
          {{ t('strictlyMode') }}
          <el-tooltip :content="t('strictlyModeTip')">
            <ma-svg-icon name="material-symbols:info-outline-rounded" :size="16" />
          </el-tooltip>
        </div>
      </el-checkbox>
      <el-checkbox @change="(value) => handleSelectAll(value as boolean)">
        {{ t('selectAll') }}
      </el-checkbox>
      <el-checkbox v-if="checkStrictly" @change="handleInvert">
        {{ t('invert') }}
      </el-checkbox>
    </div>
    <div class="flex flex-col gap-y-1">
      <div class="flex items-center justify-between gap-x-1">
        <el-input v-model="filterText" :placeholder="t('placeholder')" clearable>
          <template #prefix>
            <ma-svg-icon name="heroicons:magnifying-glass" :size="20" />
          </template>
        </el-input>
        <el-button-group class="flex justify-end">
          <el-button @click="toggle()">
            {{ t(isExpand ? 'fold' : 'open') }}
          </el-button>
          <slot name="buttons" />
        </el-button-group>
      </div>
      <div class="ma-tree-extra">
        <slot name="extra" />
      </div>
    </div>
  </div>
  <ElTree
    ref="treeRef"
    :check-strictly="checkStrictly"
    :filter-node-method="filterNode"
    class="w-auto overflow-y-auto"
    v-bind="$attrs"
  >
    <template #default="{ node, data }">
      <slot v-bind="{ node, data }" />
    </template>
  </ElTree>
</template>
