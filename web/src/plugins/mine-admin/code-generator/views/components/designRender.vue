<script lang="ts" setup>
import draggable from 'vuedraggable'
import type { Ref } from 'vue'
import type { DesignComponent } from '$/mine-admin/code-generator/configs/component.tsx'

const designComponents = inject<Ref<DesignComponent[]>>('designComponents')
const currentSelection = inject<Ref<DesignComponent>>('currentSelection')
const options = inject<Record<string, any>>('options')

// 添加到设计区域
function onAdd(event: any) {
}

function updateCurrentSelection(element: DesignComponent) {
  currentSelection!.value = designComponents?.value?.find?.((item: DesignComponent) => item.id === element.id)
}

function deleteComponent(element: DesignComponent) {
  designComponents?.value?.splice(designComponents?.value?.findIndex((item: DesignComponent) => item.id === element.id), 1)
}

function copyComponent(element: DesignComponent) {

}

onMounted(() => {
  document.querySelector('.draggableArea')!.style!.height = `${(document.querySelector('.designArea') as HTMLElement).clientHeight - 25}px`
})
</script>

<template>
  <div class="designArea min-h-[400px] w-7/12 rounded p-2 pr-4">
    <div
      v-show="designComponents?.length === 0 && !options?.isDrag"
      class="text-center"
    >
      请拖拽组件到设计器
    </div>
    <ma-form v-model="options!.model" class="w-full">
      <draggable
        :list="designComponents"
        group="components"
        :animation="150"
        chosen-class="choose"
        class="draggableArea min-h-[400px] w-full overflow-y-auto p-1"
        item-key="id"
        @start="() => options!.isDrag = false"
        @end="() => options!.isDrag = true"
        @add="onAdd"
      >
        <template #item="{ element }">
          <div
            :class="`componentStyle renderItem ${element.id === currentSelection?.id ? 'selection' : ''}`"
            @click.stop="updateCurrentSelection(element)"
          >
            <div class="text-sm">
              {{ element?.formConfig?.prop ? `字段：${element?.formConfig?.prop}` : '' }}
              {{ element?.fieldAttrs?.comment ? `，注释：${element?.fieldAttrs?.comment}` : '' }}
            </div>
            <div
              v-show="element.id === currentSelection?.id"
              class="absolute right-0 top-0 z-10 flex gap-x-3 bg-[rgb(var(--ui-primary))] p-1"
            >
              <ma-svg-icon
                name="ri:file-copy-fill"
                class="cursor-pointer text-white"
                @click="copyComponent(element)"
              />
              <ma-svg-icon
                name="ri:delete-bin-6-line"
                class="cursor-pointer text-white"
                @click="deleteComponent(element)"
              />
            </div>
            <div class="absolute left-0 top-0 z-5 h-full w-full" />
            <component
              :is="element?.formConfig?.render?.()"
              v-if="element?.formConfig?.render"
              v-model="options!.model[element?.formConfig?.prop]"
            />
            <div v-else>
              -
            </div>
          </div>
        </template>
      </draggable>
    </ma-form>
  </div>
</template>

<style scoped lang="scss">
.designArea {
  @apply bg-[rgba(255,255,255,0.7)] dark-bg-[rgba(0,0,0,0.9)]
  border-2 border-gray-300 dark-b-gray-6 border-dashed;
}
.choose, .selection {
  @apply border-2 !border-[rgb(var(--ui-primary))] shadow-lg;
}
.renderItem {
  @apply mb-3 flex flex-col gap-y-1 relative;
}
</style>
