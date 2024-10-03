import cellRender from '../utils/cellRender.ts'
import type { MaProTableExpose } from '@mineadmin/pro-table'
import type { Ref } from 'vue'

export function useCellRender(proxy?: Ref<MaProTableExpose | undefined>) {
  return cellRender((component, options) => {
    return scope => h(component, { scope, options, proxy })
  })
}

export function useCellRenderTo() {
  return cellRender((component, options) => {
    return {
      name: component.name,
      props: options,
    }
  })
}
