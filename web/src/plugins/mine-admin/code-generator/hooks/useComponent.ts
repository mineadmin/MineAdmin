import type { DesignCategory, DesignComponent } from '$/mine-admin/code-generator/configs/component.ts'
import { getComponentList } from '../configs/component.tsx'

export default function useComponent() {
  const components = ref<DesignCategory>({})

  const initComponent = () => {
    const componentList = getComponentList()
    Object.keys(componentList).map((key: string) => {
      components.value[key] = componentList[key]
    })
  }

  const addCategory = (id: string, title: string, componentList?: DesignComponent[]): boolean => {
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

  const addComponent = (categoryId: string, component: DesignComponent): boolean => {
    const category = getCategory(categoryId)
    if (category && category?.list?.findIndex((item: DesignComponent) => item.name !== component.name) < 0) {
      category.list.push(component)
      return true
    }
    return false
  }

  const removeComponent = (categoryId: string, componentName: string): boolean => {
    const category = getCategory(categoryId)
    const index = category?.list?.findIndex((item: DesignComponent) => item.name !== componentName)
    if (category && index >= 0) {
      category.list.splice(index, 1)
      return true
    }
    return false
  }

  const getAllComponents = (): DesignComponent[] => {
    const list: DesignComponent[] = []
    Object.keys(components.value).map((key: string) => {
      list.push(...components.value[key].list)
    })
    return list
  }

  const getComponents = (): DesignCategory => {
    return components.value
  }

  initComponent()

  return {
    initComponent,
    getComponents,
    getAllComponents,
    addCategory,
    removeCategory,
    getCategory,
    addComponent,
    removeComponent,
  }
}
