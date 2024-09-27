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
import type { DialogRootEmits, DialogRootProps } from 'radix-vue'

import {
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle,
  DialogTrigger,
  useForwardPropsEmits,
} from 'radix-vue'

defineOptions({ name: 'MModal' })

const props = withDefaults(
  defineProps<MDialogProps>(),
  {
    clickModalClose: true,
    modal: true,
  },
)
const emits = defineEmits<DialogRootEmits>()

interface MDialogProps extends DialogRootProps {
  clickModalClose?: boolean
  contentClass?: string
  title?: string
}

const forwarded = useForwardPropsEmits({
  modal: props?.modal,
  open: props?.open,
  defaultOpen: props?.defaultOpen,
}, emits)

const isOpen = defineModel<boolean>({ default: false })
</script>

<template>
  <DialogRoot v-bind="forwarded" :open="isOpen">
    <DialogTrigger v-show="$slots.trigger">
      <slot name="trigger" />
    </DialogTrigger>
    <DialogPortal>
      <DialogOverlay class="modal-mask" @click="isOpen = false" />
      <DialogContent class="modal-content" :class="contentClass">
        <div class="relative h-[calc(100%-35px)]">
          <DialogTitle v-show="props?.title" as="div" class="h-50px flex items-center justify-between">
            <div class="font-bold">
              {{ props?.title }}
            </div>
            <button class="close-btn" @click="() => isOpen = false">
              <ma-svg-icon name="i-carbon:close" :size="24" />
            </button>
          </DialogTitle>
          <DialogDescription>
            <slot />
          </DialogDescription>
        </div>
        <DialogClose v-show="$slots.footer" class="relative bottom-0 w-full b-0 bg-white dark-bg-dark-5">
          <div class="flex items-center justify-end">
            <slot name="footer" />
          </div>
        </DialogClose>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>

<style scoped lang="scss">
.modal-mask {
  @apply fixed inset-0 bg-gray-9/30 backdrop-blur-sm transition-opacity dark-bg-black/15 z-2999;
}

.modal-content {
  @apply transition-all ease-in-out top-0 right-0 z-3000 bg-white dark-bg-dark-5 rounded-xl
  fixed top-[50%] left-[50%] translate-x-[-50%] translate-y-[-50%] focus:outline-none z-3000
    overflow-hidden px-5 pb-5
  ;
  animation: contentShow 300ms cubic-bezier(0.16, 2, 0.5, 2);
}

@keyframes contentShow {
  from {
    opacity: 0;
    transform: translate(-50%, -48%) scale(0.96);
  }
  to {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}

.close-btn {
  @apply cursor-pointer b-0 bg-white dark-bg-dark-5;
}
</style>
