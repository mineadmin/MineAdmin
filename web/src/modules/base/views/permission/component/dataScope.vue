<script setup lang="ts">
import type { MaFormItem } from '@mineadmin/form'
import MaDictSelect from '@/components/ma-dict-picker/ma-dict-select.vue'
import { page as deptList } from '~/base/api/department.ts'
import MaTree from '@/components/ma-tree/index.vue'

defineOptions({ name: 'DataScope' })

interface DataScopeVo {
  policy_type: string
  deptIds: number[]
}

const model = defineModel()
const deptRef = ref()
const t = useTrans().globalTrans
const depts = ref<any[]>([])

deptList().then((res) => {
  depts.value = res.data?.list
  console.log(depts.value)
})

const items = ref<MaFormItem[]>([
  {
    label: '数据权限',
    prop: 'policy_type',
    render: () => MaDictSelect,
    renderProps: {
      placeholder: '请选择数据权限',
      data: [
        { label: '全部数据权限', value: 'ALL' },
        { label: '本部门数据权限', value: 'DEPT_SELF' },
        { label: '本部门及所有子部门数据权限', value: 'DEPT_TREE' },
        { label: '本人数据权限', value: 'SELF' },
        { label: '自选部门数据权限', value: 'CUSTOM_DEPT' },
        { label: '自定义函数数据权限', value: 'CUSTOM_FUNC' },
      ],
    },
  },
  {
    label: '自选部门',
    prop: 'deptIds',
    show: (item, model) => model.policy_type === 'CUSTOM_DEPT',
    render: () => MaTree,
    renderProps: {
      ref: (el: any) => deptRef.value = el,
      class: 'w-full',
      showCheckbox: true,
      treeKey: 'name',
      placeholder: t('form.pleaseSelect', { msg: t('baseRoleManage.permission') }),
      nodeKey: 'name',
      data: depts,
    },
  },
  {
    label: '调用函数名',
    prop: 'value',
    show: (item, model) => model.policy_type === 'CUSTOM_FUNC',
    render: 'input',
    renderProps: {
      placeholder: '请输入调用函数名',
    },
  },
])
</script>

<template>
  <ma-form v-model="model" :items="items" :options="{ labelWidth: '90px' }" />
</template>

<style scoped lang="scss">

</style>
