<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import { Dialog, DialogDescription, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import mergeClassName from '../../utils/mergeClassName.ts'

defineOptions({ name: 'MModal' })

const props = withDefaults(
  defineProps<{
    title?: string
    preventClose?: boolean
    class?: string | string[] | object
  }>(),
  {
    title: '',
    preventClose: false,
    class: '',
  },
)

const emit = defineEmits<{
  close: []
}>()

const isOpen = defineModel<boolean>({
  default: false,
})

const panelClass = computed(() => {
  return mergeClassName([
    'relative flex flex-1 flex-col bg-white dark-bg-dark-5 focus-outline-none rounded-lg',
  ], props.class)
})

const slots = useSlots()

const overlayTransitionClass = ref({
  enter: 'ease-in-out duration-500',
  enterFrom: 'opacity-0',
  enterTo: 'opacity-100',
  leave: 'ease-in-out duration-500',
  leaveFrom: 'opacity-100',
  leaveTo: 'opacity-0',
})

const transitionClass = computed(() => {
  return {
    enter: 'ease-out duration-300',
    enterFrom: 'opacity-0 translate-y-4 lg-translate-y-0 lg-scale-95',
    enterTo: 'opacity-100 translate-y-0 lg-scale-100',
    leave: 'ease-in duration-200',
    leaveFrom: 'opacity-100 translate-y-0 lg-scale-100',
    leaveTo: 'opacity-0 translate-y-4 lg-translate-y-0 lg-scale-95',
  }
})

function close() {
  isOpen.value = false
  emit('close')
}
</script>

<template>
  <TransitionRoot as="template" :appear="false" :show="isOpen">
    <Dialog class="fixed inset-0 z-2000 flex" @close="!preventClose && close()">
      <TransitionChild as="template" :appear="false" v-bind="overlayTransitionClass">
        <div class="modal-mask" />
      </TransitionChild>
      <div class="fixed inset-0 overflow-y-auto">
        <div class="min-h-[60%] flex items-end justify-center p-4 text-center lg:min-h-[80%] lg-items-center">
          <TransitionChild as="template" :appear="false" v-bind="transitionClass">
            <DialogPanel :class="panelClass">
              <div flex="~ items-center justify-between" p-3 border-b="~ solid stone/15" text-6>
                <DialogTitle m-0 text-lg text-dark dark-text-white>
                  {{ title }}
                </DialogTitle>
                <button class="close-btn">
                  <ma-svg-icon name="i-carbon:close" :size="24" @click="close" />
                </button>
              </div>
              <DialogDescription m-0 overflow-y-auto p-4>
                <slot />
              </DialogDescription>
              <div v-if="!!slots.footer" flex="~ items-center justify-end" px-3 py-2 border-t="~ solid stone/15">
                <slot name="footer" />
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style scoped lang="scss">
.modal-mask {
  @apply fixed inset-0 bg-gray-9/30 backdrop-blur-sm transition-opacity dark-bg-black/15; }

.close-btn {
  @apply cursor-pointer b-0 bg-white dark-bg-dark-5;
}
</style>
