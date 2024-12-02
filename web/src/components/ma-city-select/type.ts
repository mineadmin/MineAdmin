export interface ModelType {
  province?: string
  city?: string | undefined
  area?: string | undefined
}

export interface Area {
  code: string
  name: string
  children?: Area
}
