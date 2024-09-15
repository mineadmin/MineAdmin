import type { DialogEmits } from 'element-plus'
import type { MTabsOptionItems } from '$/mine-admin/basic-ui/components/tab/type.ts'

export interface Resource {
  id: number
  storage_mode: number
  origin_name: string
  object_name: string
  hash: string
  mime_type: string
  storage_path: string
  suffix: string
  size_byte: number
  size_info: string
  url: string
}

// Define the interface for FileType
export interface FileType extends MTabsOptionItems<string> {
  value: string
  label: string
  suffix: string
}

// 定义 Props 类型
export interface ResourcePanelProps {
  multiple?: boolean
  limit?: number
  pageSize?: number
  returnType?: 'id' | 'url' | 'hash'
  dbClickConfirm?: boolean
}

// 定义 Emit 事件的类型
interface ResourcePanelEmitEvents {
  cancel: void
  confirm: [selectedResources: Resource[]] // 使用数组包裹参数，表示参数列表
}

export type ResourcePanelEmits = ResourcePanelEmitEvents

export type ResourcePickerEmits = ResourcePanelEmitEvents & DialogEmits

export interface ResourcePickerProps extends ResourcePanelProps {
  visible?: boolean
}
