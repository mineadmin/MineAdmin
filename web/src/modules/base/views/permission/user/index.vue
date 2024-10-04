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
import { create, page, save } from '~/base/api/user'
import getSearchItems from './data/getSearchItems.tsx'
import getTableColumns from './data/getTableColumns.tsx'
import UserForm from './form.vue'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import useDialog from '@/hooks/useDialog.ts'
import { useMessage } from '@/hooks/useMessage.ts'

defineOptions({ name: 'permission:user' })

const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const selections = ref<any[]>([])
const i18n = useTrans() as TransType
const t = i18n.globalTrans
const msg = useMessage()

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  // 保存数据
  ok: ({ formType }) => {
    const elForm = formRef.value.maForm.getElFormRef()
    const model = formRef.value.model
    // 验证通过后
    elForm.validate().then(() => {
      // 新增
      if (formType === 'add') {
        create(model).then((res) => {
          res.code === 200 ? msg.success('添加成功') : msg.error(res.message)
          maDialog.close()
          proTableRef.value.refresh()
        }).catch((err) => {
          msg.error(err)
        })
      }
      // 修改
      else {
        save(model.id, model).then((res) => {
          res.code === 200 ? msg.success('修改成功') : msg.error(res.message)
          maDialog.close()
          proTableRef.value.refresh()
        }).catch((err) => {
          msg.error(err)
        })
      }
    }).catch()
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
  tableColumns: getTableColumns(maDialog, formRef, t),
})
</script>

<template>
  <div class="mine-layout pt-3">
    <MaProTable ref="proTableRef" :options="options" :schema="schema">
      <template #actions>
        <el-button
          type="primary" @click="() => {
            maDialog.setTitle(t('crud.add'))
            maDialog.open({ formType: 'add' })
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
          <el-button
            type="primary" @click="() => {
              maDialog.setTitle(t('crud.add'))
              maDialog.open({ formType: 'add' })
            }"
          >
            {{ t('crud.add') }}
          </el-button>
        </el-empty>
      </template>
    </MaProTable>

    <component :is="maDialog.Dialog">
      <template #default="{ formType, data }">
        <UserForm ref="formRef" :form-type="formType" :data="data" />
      </template>
    </component>
  </div>
</template>

<style scoped lang="scss">

</style>
