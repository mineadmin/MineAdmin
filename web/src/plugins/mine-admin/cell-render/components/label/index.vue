<script lang="tsx">
import type { VNode } from 'vue'
import { defineComponent } from 'vue'
import type { TableColumnRenderer } from '@mineadmin/table/dist/types/table-column'
import { makeRFV } from '../../utils/tools.ts'

// 定义map的值类型，可以是VNode、一个返回VNode的函数、字符串、null、undefined或数字
type MapValue = VNode | ((scope: TableColumnRenderer) => VNode) | string | null | undefined | number

export interface Options {
  map: Record<number, MapValue>
}

export default defineComponent({
  name: 'ma-label',
  props: {
    scope: {
      type: Object,
      default: () => ({}),
    },
    options: {
      type: Object as () => Options,
      default: () => ({
        map: {},
      }),
    },
  },
  setup(props) {
    const { value } = makeRFV(props)
    const map = props.options.map
    const v = map[value.value] ?? null
    return () => typeof v === 'function' ? v(props.scope) : v
  },
})
</script>
