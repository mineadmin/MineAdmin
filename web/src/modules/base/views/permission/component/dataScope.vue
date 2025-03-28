<script setup lang="ts">
import type { MaFormItem } from '@mineadmin/form'
import MaDictSelect from '@/components/ma-dict-picker/ma-dict-select.vue'

defineOptions({ name: 'DataScope' })

interface DataScopeVo {
  dataScope: string
  deptIds: number[]
}

const model = ref<DataScopeVo>({
  dataScope: '1',
  deptIds: [],
})

const items = ref<MaFormItem[]>([
  {
    label: '数据权限',
    prop: 'policy_type',
    render: () => MaDictSelect,
    renderProps: {
      data: [
        { label: '全部数据权限', value: 'ALL' },
        { label: '本部门数据权限', value: 'DEPT_SELF' },
        { label: '本部门及所有子部门数据权限', value: 'DEPT_TREE' },
        { label: '本人数据权限', value: 'SELF' },
        { label: '自选部门数据权限', value: 'CUSTOM_DEPT' },
        { label: '自定义数据权限', value: 'CUSTOM_FUNC' },
      ],
    },
  },
])
</script>

<template>
  <ma-form v-model="model" :items="items" />
</template>

<style scoped lang="scss">

</style>
