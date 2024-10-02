<script lang="tsx">
import { defineComponent } from 'vue'
import {
  cellRenderPluginName,
  cellRenderPluginProps,
  createOptions,
  getConfig,
  organizeProps,
} from '../../utils/tools.ts'

// 定义options类型,与ImageProps类型合并
export interface Options {

}

export default defineComponent({
  name: cellRenderPluginName('amount'),
  props: cellRenderPluginProps(),
  setup(props) {
    const { value } = organizeProps(props)
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
