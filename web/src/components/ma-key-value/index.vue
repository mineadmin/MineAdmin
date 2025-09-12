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
  parse_success: "Parse successful"
  add: "Add"
  parse_data: "Parse data"
  parse: "Parse"
zh_CN:
  parse_success: "解析成功"
  add: "添加"
  parse_data: "解析数据"
  parse: "解析"
zh_TW:
  parse_success: "解析成功"
  add: "添加"
  parse_data: "解析數據"
  parse: "解析"
</i18n>

<script setup lang="ts">
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import useDialog from '@/hooks/useDialog.ts'
import KeyValueForm from './components/form.vue'
import { ResultCode } from '@/utils/ResultCode.ts'
import { useMessage } from '@/hooks/useMessage.ts'

defineOptions({ name: 'MaKeyValue' })

const model = defineModel<any>()
const t = useTrans().globalTrans
const formRef = ref()
const msg = useMessage()

function addKeyValue() {
  model.value = [...model.value, { label: '', value: '' }]
}

// 删除指定索引的键值对
function removeKeyValue(index: number) {
  model.value.splice(index, 1) // 使用 splice 删除指定索引的元素
}

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  alignCenter: true,
  // 保存数据
  ok: ({ formType: _formType }, _okLoadingState: (state: boolean) => void) => {
    formRef.value.add().then((res: any) => {
      res.code === ResultCode.SUCCESS ? msg.success(t('parse_success')) : msg.error(res.message)
      model.value = [...model.value, ...res.data]
      maDialog.close()
    }).catch((err: any) => {
      msg.alertError(err.message)
    })
  },
})
</script>

<template>
  <div class="w-full">
    <div v-if="model?.length > 0" class="mb-3 w-full flex flex-col gap-2">
      <div v-for="(item, index) in model" :key="index" class="flex flex-row justify-between">
        <div class="key-value-input flex flex-row gap-4">
          <el-input v-model="item.label" :placeholder="`label ${index + 1}`" />
          <el-input v-model="item.value" :placeholder="`Value ${index + 1}`" />
        </div>
        <el-button circle type="danger" @click="removeKeyValue(index)">
          <ma-svg-icon name="i-heroicons:trash" />
        </el-button>
      </div>
    </div>
    <div>
      <el-button type="primary" @click="addKeyValue">
        {{ t('add') }}
      </el-button>
      <el-button
        @click="() => {
          maDialog.setTitle(t('parse_data'))
          maDialog.open({ formType: 'add' })
        }"
      >
        {{ t('parse') }}
      </el-button>
    </div>
  </div>
  <component :is="maDialog.Dialog">
    <template #default>
      <!-- 新增、编辑表单 -->
      <KeyValueForm ref="formRef" />
    </template>
  </component>
</template>

<style scoped lang="scss"></style>
