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
zh_CN:
  placeholder: 搜索名称
  open: 展开
  fold: 折叠
zh_TW:
  placeholder: 檢索名稱
  open: 展開
  fold: 收起
</i18n>

<script setup lang="ts">
import { ElTree } from 'element-plus'
import { get } from 'lodash-es'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MaTree' })

const { treeKey = 'label' } = defineProps<{
  treeKey: string
}>()

const t = useLocalTrans()

const filterText = ref<string>('')
const treeRef = ref<InstanceType<typeof ElTree>>()

watch(() => filterText.value, (val) => {
  treeRef.value!.filter(val)
})

function filterNode(value: string, data: Record<string, any>) {
  if (!value) {
    return true
  }
  return get(data, treeKey)!.includes(value)
}

function toggle(state: boolean) {
  const nodes = treeRef.value!.store?._getAllNodes()
  nodes.forEach((item) => {
    item.expanded = state
  })
}
</script>

<template>
  <div class="sticky flex items-center justify-between gap-x-1">
    <el-input v-model="filterText" :placeholder="t('placeholder')" clearable>
      <template #prefix>
        <ma-svg-icon name="heroicons:magnifying-glass" :size="20" />
      </template>
    </el-input>
    <el-button-group class="flex justify-end">
      <el-button @click="toggle(true)">
        {{ t('open') }}
      </el-button>
      <el-button @click="toggle(false)">
        {{ t('fold') }}
      </el-button>
    </el-button-group>
  </div>
  <ElTree
    v-bind="$attrs"
    ref="treeRef"
    :filter-node-method="filterNode"
    class="overflow-y-auto"
  >
    <template #default="{ node, data }">
      <slot v-bind="{ node, data }" />
    </template>
  </ElTree>
</template>
