<script setup lang="ts">
import type { MaFormItem } from '@mineadmin/form'
import { page as deptList } from '~/base/api/department.ts'
import MaTree from '@/components/ma-tree/index.vue'
import MaDictSelect from '@/components/ma-dict-picker/ma-dict-select.vue'

defineOptions({ name: 'DataScope' })

const { label = '名称' } = defineProps<{ label?: string }>()

const model = defineModel()
const deptRef = ref()
const t = useTrans().globalTrans
const depts = ref<any[]>([])

deptList().then((res) => {
  depts.value = res.data?.list
})

const items = ref<MaFormItem[]>([
  {
    label: () => label,
    prop: 'name',
    render: 'input',
    renderProps: {
      disabled: true,
    },
  },
  {
    label: () => t('basePost.dataScope'),
    prop: 'policy_type',
    render: () => MaDictSelect,
    renderProps: {
      placeholder: t('form.pleaseSelect', { msg: t('basePost.dataScope') }),
      dictName: 'data-scope',
      clearable: true,
      onClear: () => {
        model.value = {}
      },
    },
  },
  {
    label: () => t('basePost.selectDept'),
    prop: 'value',
    show: () => model.value?.policy_type === 'CUSTOM_DEPT',
    render: () => MaTree,
    renderProps: {
      ref: (el: any) => deptRef.value = el,
      class: 'w-full',
      showCheckbox: true,
      props: { label: 'name' },
      placeholder: t('form.pleaseSelect', { msg: t('basePost.selectDept') }),
      nodeKey: 'id',
      data: depts,
    },
  },
  {
    label: () => t('basePost.callFunc'),
    prop: 'func_name',
    show: () => model.value?.policy_type === 'CUSTOM_FUNC',
    render: 'input',
    renderProps: {
      placeholder: t('form.pleaseInput', { msg: t('basePost.callFunc') }),
    },
  },
])

defineExpose({ deptRef })
</script>

<template>
  <ma-form v-model="model" :items="items" :options="{ labelWidth: '90px' }" />
</template>

<style scoped lang="scss">

</style>
