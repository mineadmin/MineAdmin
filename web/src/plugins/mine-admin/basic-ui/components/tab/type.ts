export interface OptionItems<T> {
  label: string | (() => string)
  value: T
  icon?: string
  [key: string]: any
}
