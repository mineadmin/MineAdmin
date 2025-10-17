import type { CodeGeneratorComponent, CodeGeneratorComponentList } from '$/mine-admin/code-generator/configs/component.ts'
import { componentList } from '$/mine-admin/code-generator/configs/component.ts'

export default function useComponent() {
  const components = ref<CodeGeneratorComponentList>({})

  const initComponent = () => {
    Object.keys(componentList).map((key: string) => {
      components.value[key] = componentList[key]
    })
  }

  const addCategory = (id: string, title: string, componentList?: CodeGeneratorComponent[]): boolean => {
    if (!components.value[id]) {
      components.value.push[id] = {
        title,
        list: componentList ?? [],
      }
      return true
    }
    return false
  }

  const removeCategory = (id: string): boolean => {
    if (components.value[id]) {
      delete components.value[id]
      return true
    }
    return false
  }

  const getCategory = (id: string): any => {
    return components.value[id] ?? null
  }

  const addComponent = (categoryId: string, component: CodeGeneratorComponent): boolean => {
    const category = getCategory(categoryId)
    if (category && category?.list?.findIndex((item: CodeGeneratorComponent) => item.name !== component.name) < 0) {
      category.list.push(component)
      return true
    }
    return false
  }

  const removeComponent = (categoryId: string, componentName: string): boolean => {
    const category = getCategory(categoryId)
    const index = category?.list?.findIndex((item: CodeGeneratorComponent) => item.name !== componentName)
    if (category && index >= 0) {
      category.list.splice(index, 1)
      return true
    }
    return false
  }

  const getComponents = () => components.value

  return {
    initComponent,
    getComponents,
    addCategory,
    removeCategory,
    getCategory,
    addComponent,
    removeComponent,
  }
}
