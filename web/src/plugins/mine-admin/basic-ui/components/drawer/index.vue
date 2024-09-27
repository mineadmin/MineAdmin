<script setup lang="ts">
import type { DialogRootEmits } from 'radix-vue'
import type { DrawerRootProps } from 'vaul-vue'

import { OverlayScrollbarsComponent } from 'overlayscrollbars-vue'
import {
  useForwardPropsEmits,
} from 'radix-vue'

import {
  DrawerClose, DrawerContent,
  DrawerDescription,
  DrawerOverlay,
  DrawerPortal,
  DrawerRoot,
  DrawerTitle,
  DrawerTrigger,
} from 'vaul-vue'

defineOptions({ name: 'MDrawer' })

const props = withDefaults(
  defineProps<MDrawerProps>(),
  {
    clickModalClose: true,
    side: 'right',
    modal: true,
    direction: 'right',
    nested: true,
  },
)
const emits = defineEmits<DialogRootEmits>()

interface MDrawerProps extends DrawerRootProps {
  clickModalClose?: boolean
  contentClass?: string
  title?: string
}

const forwarded = useForwardPropsEmits({
  activeSnapPoint: props?.activeSnapPoint,
  closeThreshold: props?.closeThreshold,
  shouldScaleBackground: props?.shouldScaleBackground,
  scrollLockTimeout: props?.scrollLockTimeout,
  fixed: props?.fixed,
  dismissible: props?.dismissible,
  modal: props?.modal,
  open: props?.open,
  defaultOpen: props?.defaultOpen,
  nested: props?.nested,
  direction: props?.direction,
}, emits)

const isOpen = defineModel<boolean>()
</script>

<template>
  <div>
    <DrawerRoot
      v-bind="forwarded" :open="isOpen" @release="(e) => {
        isOpen = e
      }"
    >
      <DrawerTrigger class="drawer-trigger-button">
        <slot name="trigger" />
      </DrawerTrigger>
      <DrawerPortal>
        <Transition name="fade">
          <DrawerOverlay
            v-if="isOpen"
            class="drawer-mask w-full"
            data-dismissable-modal="true"
            @click="() => {
              if (props?.clickModalClose) {
                isOpen = false
              }
            }"
          />
        </Transition>
        <DrawerContent
          class="drawer-content" :class="{
            [props?.contentClass]: true,
          }"
        >
          <DrawerTitle as="div">
            <div class="h-50px flex items-center justify-between px-3">
              <div class="font-bold">
                {{ props?.title }}
              </div>
              <button class="close-btn" @click="() => isOpen = false">
                <ma-svg-icon name="i-carbon:close" :size="24" />
              </button>
            </div>
          </DrawerTitle>
          <DrawerDescription
            :class="{
              'h-[calc(100%-100px)]': $slots.footer,
              'h-[calc(100%-50px)]': !$slots.footer,
            }"
          >
            <OverlayScrollbarsComponent :options="{ scrollbars: { visibility: 'hidden' } }" defer class="h-[calc(100%-30px)] p-3">
              <slot />
            </OverlayScrollbarsComponent>
          </DrawerDescription>
          <DrawerClose v-show="$slots.footer" class="h-50px w-full b-0 b-t-1 b-t-gray-3 rounded-l-xl rounded-t-none b-t-solid bg-white dark-b-t-dark-3 dark-bg-dark-5">
            <div class="px-2">
              <slot name="footer" />
            </div>
          </DrawerClose>
        </DrawerContent>
      </drawerportal>
    </DrawerRoot>
  </div>
</template>

<style lang="scss" scoped>
.drawer-mask {
  @apply fixed inset-0 bg-gray-9/30 backdrop-blur-sm transition-opacity dark-bg-black/15 z-2999 h-full;
}
.drawer-trigger-button {
  @apply b-0 bg-blue/0 relative top-2px outline-none ring-none
}

.drawer-content {
  @apply absolute transition-all ease-in-out top-0 right-0
  z-3000 h-full bg-white dark-bg-dark-5 rounded-l-xl
  ;
}

.close-btn {
  @apply cursor-pointer b-0 bg-white dark-bg-dark-5;
}
</style>
