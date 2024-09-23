import type { Options as ImageOptions } from '../components/image/index.vue'
import type { Options as LabelOptions } from '../components/label/index.vue'
import type { Options as SwitchOptions } from '../components/switch/index.vue'
import type { Options as UserOptions } from '../components/user/index.vue'

interface Config {
  image?: Partial<ImageOptions>
  label?: Partial<LabelOptions>
  switch?: Partial<SwitchOptions>
  user?: Partial<UserOptions>
}
export type {
  Config,
  ImageOptions,
  LabelOptions,
  SwitchOptions,
  UserOptions
}
