<script lang="ts" setup>
import draggable from 'vuedraggable'
import type { DesignComponent } from '$/mine-admin/code-generator/configs/component.tsx'
import { clone, uid } from 'radash'

const options = inject<Record<string, any>>('options', {})
const componentHook = inject<any>('componentHook')

// 克隆组件
function cloneComponent(element: DesignComponent) {
  const component: DesignComponent = {
    ...element,
    formConfig: clone(Object.assign(element?.formConfig ?? {}, { prop: `field_name_${uid(5)}` })),
    columnConfig: clone(element?.columnConfig ?? {}),
    fieldAttrs: clone(element?.fieldAttrs),
    id: uid(7, 'abcdefg1234567'),
  }
  options.value.model[component?.formConfig?.prop] = component?.fieldAttrs?.defaultValue ?? null
  return component
}
</script>

<template>
  <div class="componentList h-full w-2.5/12 rounded bg-white dark-bg-dark-8">
    <div class="overflow-hidden p-2">
      <el-collapse v-model="options.componentCollapseModel">
        <template v-for="(category, name) in componentHook.getComponents()" :key="name">
          <el-collapse-item :title="category.title" :name="name">
            <draggable
              :list="category.list"
              :group="{ name: 'components', pull: 'clone', put: false }"
              :sort="false"
              item-key="name"
              :clone="cloneComponent"
              class="grid grid-cols-2 gap-3"
              :animation="150"
              @start="() => options.isDrag = true"
              @end="() => options.isDrag = false"
            >
              <template #item="{ element }">
                <div class="componentStyle cursor-move">
                  {{ element.title }}
                </div>
              </template>
            </draggable>
          </el-collapse-item>
        </template>
      </el-collapse>
    </div>
  </div>
</template>
