import cellRender from '../utils/cellRender.ts'

export function useCellRender() {
  return cellRender((component, options) => {
    return scope => h(component, { scope, options })
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
