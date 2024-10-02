import type { Options as ImageOptions } from '../components/image/index.vue'
import type { Options as LabelOptions } from '../components/label/index.vue'
import type { Options as SwitchOptions } from '../components/switch/index.vue'
import type { Options as UserOptions } from '../components/user/index.vue'
import type { Options as ButtonsOptions } from '../components/buttons/index.vue'
import type { TableColumnScope } from '@mineadmin/table'

interface Config {
  image?: Partial<ImageOptions>
  label?: Partial<LabelOptions>
  switch?: Partial<SwitchOptions>
  user?: Partial<UserOptions>
  buttons?: Partial<ButtonsOptions>
}
export type {
  ButtonsOptions,
  Config,
  ImageOptions,
  LabelOptions,
  SwitchOptions,
  UserOptions,
}

export interface StandardInput {
  value: any
  row: any
  scope: TableColumnScope
}
