export interface MTabsOptionItems<T> {
  label: string | (() => string)
  value: T
  icon?: string
  [key: string]: any
}

// 定义MaTbs组件的props类型
export interface MTabsProps<T> {
  options: MTabsOptionItems<T>[]
  direction?: 'horizontal' | 'vertical'
  align?: 'start' | 'center' | 'end'
}

// 定义MaTbs组件的emit事件类型
export interface MTabsEmits {
  (event: 'change', value: any, optionItem: MTabsOptionItems<any>): void
}
