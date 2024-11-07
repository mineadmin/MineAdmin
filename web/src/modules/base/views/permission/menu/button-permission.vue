<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { MaTableExpose } from '@mineadmin/table'
import useTable from '@/hooks/useTable.ts'
import type { MenuVo } from '~/base/api/menu.ts'

const emit = defineEmits<{
  (event: 'add-btn', value: MenuVo): void
}>()
const t = useTrans().globalTrans
const data = ref<any[]>([])
const buttonFormTable = ref()

function addItem() {
  emit('add-btn', { id: undefined, title: '', code: '', i18n: '', type: 'B' })
}

useTable('buttonFormTable').then((table: MaTableExpose) => {
  table.setColumns([
    {
      label: '#',
      showOverflowTooltip: false,
      width: '80px',
      headerRender: () => (
        <el-button
          size="small"
          circle
          type="primary"
          onClick={() => addItem()}
        >
          <ma-svg-icon name="ic:round-plus" size={20} />
        </el-button>
      ),
      cellRender: ({ $index }): any => (
        <el-button
          size="small"
          circle
          type="danger"
          onClick={() => {
            if (data.value.length > 0) {
              data.value.splice($index, 1)
            }
          }}
        >
          <ma-svg-icon name="ic:round-minus" size={20} />
        </el-button>
      ),
    },
    {
      label: () => t('baseMenuManage.BtnPermission.name'),
      cellRender: ({ row }): any => (
        <el-input v-model={row.title} placeholder={t('baseMenuManage.placeholder.btnName')} />
      ),
    },
    {
      label: () => t('baseMenuManage.BtnPermission.code'),
      cellRender: ({ row }): any => (
        <el-input v-model={row.code} placeholder={t('baseMenuManage.placeholder.btnCode')} />
      ),
    },
    {
      label: () => t('baseMenuManage.BtnPermission.i18n'),
      cellRender: ({ row }): any => (
        <el-input v-model={row.i18n} placeholder={t('baseMenuManage.placeholder.btnI18n')} />
      ),
    },
  ])
})

function setBtnData(btn: any[]) {
  data.value = []
  btn.map((item: any) => {
    item.type === 'B' && data.value.push(item)
  })

  buttonFormTable.value?.setData(data.value)
}

defineExpose({ setBtnData })
</script>

<template>
  <el-card class="w-full" shadow="never">
    <ma-table ref="buttonFormTable">
      <template #empty>
        <div class="flex items-center justify-center gap-x-2">
          {{ t('baseMenuManage.BtnPermission.noBtn') }}
          <el-button type="primary" plain @click="addItem">
            {{ t('baseMenuManage.BtnPermission.add') }}
          </el-button>
        </div>
      </template>
    </ma-table>
  </el-card>
</template>

<style scoped lang="scss">

</style>
