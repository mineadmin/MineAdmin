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
      <el-form
        v-model="currentSelection"
      >
        <el-card>
          <template #header>
            <span>物理字段</span>
          </template>
          <el-form-item label="字段名称">
            <ElInput
              v-model="formConfig.prop"
              @change="() => {
                currentSelection?.initHandle?.(formConfig?.prop)
              }"
            />
          </el-form-item>
          <el-form-item label="字段注释">
            <ElInput v-model="fieldAttrs.comment" />
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
        </el-card>
        <el-card class="mt-2">
          <template #header>
            <span>渲染配置</span>
          </template>
          <el-form-item label="表单组件">
            <el-select-v2
              v-model="currentSelection.name"
              :options="components"
              @change="(v: string) => {
                const item = components.find((item: any) => item.value === v)?.item
                options.model[formConfig.prop] = item?.formConfig?.defaultValue ?? ''
                formConfig!.render = item?.formConfig?.render ?? (() => ElInput)
              }"
            />
          </el-form-item>
        </el-card>
      </el-form>
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
