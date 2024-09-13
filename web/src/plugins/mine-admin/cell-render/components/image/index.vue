<script lang="tsx">
import type { PropType } from 'vue'
import { defineComponent } from 'vue'
import type { ImageEmits, ImageProps } from 'element-plus'
import type { WithOnEventListeners } from '../../utils/tools.ts'
import { cellRenderPluginName, createOptions, createRowFieldValues, getConfig } from '../../utils/tools.ts'

export interface Emits extends ImageEmits {
}

// 定义options类型,与ImageProps类型合并
export interface Options extends Omit<Partial<ImageProps>, 'previewSrcList'>, WithOnEventListeners<Emits> {
  style?: Record<string, any>
  class?: any
  width?: number
  height?: number
  preview?: boolean
  radius?: number
}

export default defineComponent({
  name: cellRenderPluginName('image'),
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
    const style = computed(() => {
      return {
        width: `${options.value.width ?? 40}px`,
        height: `${options.value.height ?? 40}px`,
        borderRadius: `${options.value.radius ?? 0}px`,
        ...options.value.style,
      }
    })

    const previewSrcList = computed(() => {
      if (options.value.preview) {
        return [value.value]
      }
      return []
    })

    const bind = computed(() => {
      const {
        width,
        height,
        src,
        style,
        ...rest
      } = options.value
      return rest
    })

    return () => (
      <el-image style={style.value} src={value.value} preview-src-list={previewSrcList.value} {...bind.value} />
    )
  },
})
</script>
