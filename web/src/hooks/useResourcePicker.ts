import { h, render } from 'vue'
import MaResourcePicker from '@/components/ma-resource-picker/index.vue'
import type { ResourcePickerEmits, ResourcePickerProps } from '@/components/ma-resource-picker/type.ts'

export type WithOnEventListeners<T> = {
  [K in keyof T as `on${Capitalize<string & K>}`]?: T[K];
}

type Options = Partial<ResourcePickerProps & WithOnEventListeners<ResourcePickerEmits>>
export function useResourcePicker(options?: Omit<Options, 'onClosed' | 'visible'>) {
  const resourcePickerDom = document.createElement('div')

  const props: Options = {
    ...options,
    visible: true,
    onClosed() {
      render(null, resourcePickerDom)
      if (document.body.contains(resourcePickerDom)) {
        document.body.removeChild(resourcePickerDom)
      }
      return true
    },
  }

  const vnode = h(MaResourcePicker, props)
  document.body.appendChild(resourcePickerDom)
  render(vnode, resourcePickerDom)
}
