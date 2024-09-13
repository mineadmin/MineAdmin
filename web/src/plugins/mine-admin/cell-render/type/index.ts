import type { Options as ImageOptions } from '../components/image/index.vue'
import type { Options as LabelOptions } from '../components/label/index.vue'

interface Config {
  image?: Partial<ImageOptions>
  label?: Partial<LabelOptions>
}
export type {
  Config,
  ImageOptions,
  LabelOptions,
}
