<script setup lang="ts">
import { Dialog, DialogDescription, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { OverlayScrollbarsComponent } from 'overlayscrollbars-vue'
import mergeClassName from '../../utils/mergeClassName.ts'

defineOptions({ name: 'MDrawer' })

const props = withDefaults(
  defineProps<{
    title?: string
    position?: 'left' | 'right'
    class?: string | string[] | object
  }>(),
  {
    title: '',
    position: 'right',
    class: '',
  },
)

const emit = defineEmits<{
  close: []
}>()

const isOpen = defineModel<boolean>({ default: false })

const slots = useSlots()

const panelClass = computed(() => {
  return mergeClassName([
    'relative flex flex-1 flex-col bg-white dark-bg-dark-5 focus-outline-none rounded-l-lg',
  ], props.class)
})

const overlayTransitionClass = ref({
  enter: 'ease-in-out duration-300',
  enterFrom: 'opacity-0',
  enterTo: 'opacity-100',
  leave: 'ease-in-out duration-300',
  leaveFrom: 'opacity-100',
  leaveTo: 'opacity-0',
})

const transitionClass = computed(() => {
  return {
    enter: 'transform transition ease-in-out duration-300',
    leave: 'transform transition ease-in-out duration-200',
    enterFrom: props.position === 'left' ? '-translate-x-full' : 'translate-x-full',
    enterTo: 'translate-x-0',
    leaveFrom: 'translate-x-0',
    leaveTo: props.position === 'left' ? '-translate-x-full' : 'translate-x-full',
  }
})

function close() {
  isOpen.value = false
  emit('close')
}
</script>

<template>
  <TransitionRoot as="template" :appear="false" :show="isOpen">
    <Dialog class="fixed inset-0 z-2000 flex" :class="{ 'justify-end': position === 'right' }" @close="close()">
      <TransitionChild as="template" :appear="false" v-bind="overlayTransitionClass">
        <div class="drawer-mask" />
      </TransitionChild>
      <TransitionChild as="template" :appear="false" v-bind="transitionClass">
        <DialogPanel :class="panelClass">
          <div flex="~ items-center justify-between" p-3 border-b="~ solid stone/15" text-4>
            <DialogTitle class="m-0 text-lg text-dark dark-text-white">
              {{ title }}
            </DialogTitle>
            <button class="close-btn">
              <ma-svg-icon name="i-carbon:close" :size="24" @click="close" />
            </button>
          </div>
          <DialogDescription class="m-0 flex-1 of-y-hidden" :class="{ '!pb-6': !slots.footer }">
            <OverlayScrollbarsComponent :options="{ scrollbars: { autoHide: 'leave', autoHideDelay: 300 } }" defer class="h-full p-3">
              <slot />
            </OverlayScrollbarsComponent>
          </DialogDescription>
          <div v-if="!!slots.footer" flex="~ items-center justify-end" px-3 py-2 border-t="~ solid stone/15">
            <slot name="footer" />
          </div>
        </DialogPanel>
      </TransitionChild>
    </Dialog>
  </TransitionRoot>
</template>

<style lang="scss" scoped>
.drawer-mask {
  @apply fixed inset-0 bg-gray-9/30 backdrop-blur-sm transition-opacity dark-bg-black/15; }

.close-btn {
  @apply cursor-pointer b-0 bg-white dark-bg-dark-5;
}
</style>
