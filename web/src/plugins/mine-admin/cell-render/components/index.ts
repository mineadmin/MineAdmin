// components/index.ts
import content from './content/index.vue'
import type { Options as ContentOptions } from './content/index.vue'

import buttons from './buttons/index.vue'
import type { Options as ButtonsOptions } from './buttons/index.vue'

import url from './url/index.vue'
import type { Options as UrlOptions } from './url/index.vue'

import images from './images/index.vue'
import type { Options as ImagesOptions } from './images/index.vue'

import label from './label/index.vue'
import type { Options as LabelOptions } from './label/index.vue'

export const components = {
  content,
  buttons,
  url,
  images,
  label,
}
// 导出组件的 Options 类型
export interface ComponentOptions {
  content: ContentOptions
  buttons: ButtonsOptions
  url: UrlOptions
  images: ImagesOptions
  label: LabelOptions
}
export type ComponentName = keyof typeof components
