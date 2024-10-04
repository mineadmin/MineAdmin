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
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { Ref } from 'vue'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import { page } from '~/base/api/user'
import getSearchItems from './data/getSearchItems.tsx'
import getTableColumns from './data/getTableColumns.tsx'
import UserForm from './form.vue'
import useDialog from '@/hooks/useDialog.ts'

defineOptions({ name: 'permission:user' })

const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const selections = ref<any[]>([])
const i18n = useTrans() as TransType
const t = i18n.globalTrans

// 弹窗配置
const { Dialog, open, setTitle } = useDialog({
  // 新增 or 保存
  onOk: () => {
    // 保存
    formRef.value?.validate().then(() => {
      // TODO: 保存
    })
  },
})

// 参数配置
const options = ref<MaProTableOptions>({
  // 表格距离底部的像素偏移适配
  adaptionOffsetBottom: 161,
  header: {
    mainTitle: () => t('baseUser.mainTitle'),
    subTitle: () => t('baseUser.subTitle'),
  },
  // 表格参数
  tableOptions: {
    on: {
      // 表格选择事件
      onSelectionChange: (selection: any[]) => selections.value = selection,
    },
  },
  // 搜索参数
  searchOptions: {
    fold: true,
    text: {
      searchBtn: () => t('crud.search'),
      resetBtn: () => t('crud.reset'),
      isFoldBtn: () => t('crud.searchFold'),
      notFoldBtn: () => t('crud.searchUnFold'),
    },
  },
  // 搜索表单参数
  searchFormOptions: { labelWidth: '90px' },
  // 请求配置
  requestOptions: {
    api: page,
  },
})
// 架构配置
const schema = ref<MaProTableSchema>({
  // 搜索项
  searchItems: getSearchItems(t),
  // 表格列
  tableColumns: getTableColumns(proTableRef, { open, setTitle }, t),
})
</script>

<template>
  <div class="mine-layout pt-3">
    <MaProTable ref="proTableRef" :options="options" :schema="schema">
      <template #actions>
        <el-button
          type="primary" @click="() => {
            setTitle(t('crud.add'))
            open({ formType: 'add' })
          }"
        >
          {{ t('crud.add') }}
        </el-button>
      </template>

      <template #toolbarLeft>
        <el-button type="danger" plain :disabled="selections.length < 1">
          {{ t('crud.delete') }}
        </el-button>
      </template>
      <!-- 数据为空时 -->
      <template #empty>
        <el-empty>
          <el-button type="primary" @click="() => open()">
            {{ t('crud.add') }}
          </el-button>
        </el-empty>
      </template>
    </MaProTable>

    <Dialog>
      <template #default="{ formType, data }">
        <UserForm :form-type="formType" :data="data" />
      </template>
    </Dialog>
  </div>
</template>

<style scoped lang="scss">

</style>
