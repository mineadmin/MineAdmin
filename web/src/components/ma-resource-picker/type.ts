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
export interface ResourcePanelEmits {
  // 事件暂时未使用,等待后续接入 这些事件只是假设有 参数也是还未确定的,等后续开发确定
  (event: 'cancel'): void // 用户点击取消
  (event: 'confirm', selectedResources: Resource[]): void // 用户确认选中的资源
  (event: 'selectionChange', selectedResources: Resource[]): void // 用户选择的资源发生变化
  (event: 'search', query: string): void // 用户进行搜索操作
  (event: 'preview', resource: Resource): void // 用户请求预览资源
}
