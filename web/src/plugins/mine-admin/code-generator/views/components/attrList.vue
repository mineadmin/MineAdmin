<script setup lang="ts">
import type { DesignComponent } from '$/mine-admin/code-generator/configs/component.tsx'
import { ElInput } from 'element-plus'

const options = inject<any>('options')
const currentSelection = inject<any>('currentSelection')
const componentHook = inject<any>('componentHook')

const formConfig = computed(() => {
  return currentSelection?.value?.formConfig ?? {}
})
const fieldAttrs = computed(() => {
  return currentSelection?.value?.fieldAttrs ?? {}
})

const fieldType = ref([
  { label: 'bigInt', value: 'bigint' },
  { label: 'decimal', value: 'decimal' },
  { label: 'string', value: 'string' },
  { label: 'date', value: 'date' },
  { label: 'datetime', value: 'datetime' },
  { label: 'text', value: 'text' },
  { label: 'longText', value: 'longtext' },
  { label: 'json', value: 'json' },
])

const components = ref<Record<string, any>[]>([])

componentHook.value.getAllComponents()?.map((item: DesignComponent) => {
  components.value.push({
    label: item?.title,
    value: item?.name,
    item,
  })
})

onMounted(() => {
  document.querySelector('.attrList')!.style!.height = `${(document.querySelector('.designArea') as HTMLElement).clientHeight - 14}px`
})
</script>

<template>
  <div class="attrList">
    <div v-if="!currentSelection" class="mt-10 text-center text-[14px]">
      请点击选择组件
    </div>
    <div v-else>
      <ma-form
        v-model="currentSelection"
      >
        <el-card>
          <template #header>
            <span>物理字段</span>
          </template>
          <el-form-item label="字段名称">
            <ElInput
              v-model="formConfig.prop"
              @change="(value: string) => {
                currentSelection!.formConfig!.prop = value
                options.model[formConfig.prop] = fieldAttrs?.defaultValue ?? null
              }"
            />
            <div class="text-red-400">
              不要忘记修改字段名
            </div>
          </el-form-item>
          <el-form-item label="字段注释">
            <ElInput v-model="fieldAttrs.comment" type="textarea" />
            <div class="text-gray-400">
              字典，例：op1=正常,op2=禁用
            </div>
          </el-form-item>
          <el-form-item v-if="fieldAttrs.type" label="字段类型">
            <el-select-v2 v-model="fieldAttrs.type" :options="fieldType" />
          </el-form-item>
          <el-form-item v-if="fieldAttrs.len" label="字段长度">
            <el-input-number v-model="fieldAttrs.len" setp="1" />
          </el-form-item>
          <el-form-item v-if="fieldAttrs.type === 'decimal'" label="小数点位">
            <el-input-number v-model="fieldAttrs.decimal" :min="0" :max="6" />
          </el-form-item>
          <el-form-item label="默认类型">
            <el-select v-model="fieldAttrs.defaultType">
              <template #label="{ label }">
                {{ label }}
              </template>
              <el-option value="value" label="输入默认值" />
              <el-option value="empty" label="空字符串" />
              <el-option value="nullable" label="NULL" />
              <el-option value="none" label="无" />
            </el-select>
          </el-form-item>
          <el-form-item v-show="fieldAttrs.defaultType === 'value'" label="默认内容">
            <ElInput v-model="fieldAttrs.defaultValue" />
          </el-form-item>
          <div class="grid grid-cols-3 gap-1">
            <el-form-item label="主键">
              <el-switch v-model="fieldAttrs.primaryKey" />
            </el-form-item>
            <el-form-item label="自增">
              <el-switch v-model="fieldAttrs.autoIncrement" />
            </el-form-item>
            <el-form-item label="可空">
              <el-switch v-model="fieldAttrs.allowNull" />
            </el-form-item>
            <el-form-item label="索引">
              <el-switch v-model="fieldAttrs.isIndex" />
            </el-form-item>
            <el-form-item label="唯一">
              <el-switch v-model="fieldAttrs.isUnique" />
            </el-form-item>
            <el-form-item label="符号">
              <el-switch v-model="fieldAttrs.unsigned" />
            </el-form-item>
          </div>
        </el-card>
        <el-card class="mt-2">
          <template #header>
            <span>表单配置</span>
          </template>
          <el-form-item label="表单组件">
            <el-select-v2
              v-model="currentSelection.name"
              :options="components"
              @change="(v: string) => {
                const item = components.find((item: any) => item.value === v)?.item
                options.model[formConfig.prop] = item?.fieldAttrs?.defaultValue ?? null
                currentSelection?.initHandle?.(formConfig?.prop)
                formConfig!.render = item.name === 'primary-key' ? null : item?.formConfig?.render ?? (() => ElInput)
              }"
            />
          </el-form-item>
          <el-form-item label="帮助信息">
            <ElInput v-model="formConfig.help" placeholder="信息将显示在表单组件下方" />
          </el-form-item>
          <el-form-item label="额外信息">
            <ElInput v-model="formConfig.extra" placeholder="信息将显示在帮助信息下方" />
          </el-form-item>
          <el-form-item label="数据验证">
            <el-select v-model="formConfig.itemProps.rules" />
          </el-form-item>
          <el-form-item label="错误提示">
            <ElInput v-model="formConfig.validate" type="textarea" placeholder="验证失败后的错误提示" />
          </el-form-item>
        </el-card>
      </ma-form>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.attrList {
  @apply w-2.5/12 rounded bg-[rgba(255,255,255,0.9)] dark-bg-[rgba(0,0,0,0.9)]
    b-1 b-solid b-gray-2 dark-b-gray-6 p-2 overflow-y-auto
  ;
}
</style>
