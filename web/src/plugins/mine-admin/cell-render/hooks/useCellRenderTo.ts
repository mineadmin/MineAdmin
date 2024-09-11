// useCellRenderTo.ts
import type { ComponentName, ComponentOptions } from '../components'

/**
 * 使用指定的组件和选项来渲染表格单元格
 * @param name
 * @param options
 */
export function useCellRenderTo<T extends ComponentName>(name: T, options?: ComponentOptions[T]) {
  // 根据组件名称和 options 返回相应的渲染信息
  const prefix = 'ma-'
  return {
    name: `${prefix}${name}`,
    props: options,
  }
}
