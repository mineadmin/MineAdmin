<script lang="tsx">
import type { PropType } from 'vue'
import { defineComponent } from 'vue'
import { cellRenderPluginName, createOptions, createRowFieldValues, getConfig } from '../../utils/tools.ts'

// 定义options类型,与ImageProps类型合并
export interface Options {

}

export default defineComponent({
  name: cellRenderPluginName('amount'),
  props: {
    scope: {
      type: Object,
      default: () => ({}),
    },
    options: {
      type: Object as PropType<Options>,
      default: () => ({}),
    },
  },
  setup(props) {
    const { value } = createRowFieldValues(props)
    const options = createOptions(props, getConfig('image'))

    const bind = computed(() => {
      const {
        ...rest
      } = options.value
      return rest
    })

    return () => `${value.value}元`
  },
})
</script>
