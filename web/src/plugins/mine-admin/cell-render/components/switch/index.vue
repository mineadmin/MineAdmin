<script lang="tsx">
import type { PropType } from 'vue'
import { defineComponent } from 'vue'
import type { SwitchEmits, SwitchProps } from 'element-plus'
import type { WithOnEventListeners } from '../../utils/tools.ts'
import { cellRenderPluginName, createOptions, createRowFieldValues, exec, getConfig } from '../../utils/tools.ts'
import { useMessage } from '@/hooks/useMessage.ts'

export interface Emits extends SwitchEmits {
}
// 定义options类型,与ImageProps类型合并
export interface Options extends Omit<Partial<SwitchProps>, 'loading' | 'beforeChange'>, WithOnEventListeners<Emits> {
  api: ((data) => Promise<any>) | string
  beforeChange?: (newValue, row, scope) => boolean | Promise<boolean>
}

export default defineComponent({
  name: cellRenderPluginName('switch'),
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
    const Message = useMessage()
    const { value, row, field } = createRowFieldValues(props)
    const options = createOptions(props, getConfig('switch'))
    const rowKey = props.scope.options?.rowKey ?? 'id'
    const loading = ref(false)

    const activeValue = computed(() => {
      return options.value.activeValue ?? true
    })
    const inactiveValue = computed(() => {
      return options.value.inactiveValue ?? false
    })
    const nextValue = computed(() => {
      return value.value === activeValue.value ? inactiveValue.value : activeValue.value
    })
    const api = typeof options.value.api === 'string' ? data => useHttp().put(options.value.api, data) : options.value.api
    const beforeChange = options.value.beforeChange ?? (() => true) // beforeChange可能是个promise

    const onBeforeChange = async () => {
      loading.value = true

      if (!await exec(beforeChange, nextValue.value, row.value, props.scope)) {
        loading.value = false
        return false
      }

      return api({
        [rowKey]: row.value[rowKey],
        field: field.value,
        value: nextValue.value,
      }).then(() => {
        Message.success('操作成功')
        value.value = nextValue.value
      }).finally(() => {
        loading.value = false
      })
    }

    const bind = computed(() => {
      const {
        beforeChange,
        ...rest
      } = options.value
      return rest
    })

    return () => (
      <el-switch loading={loading.value} model-value={value.value} before-change={onBeforeChange} {...bind.value}></el-switch>
    )
  },
})
</script>

<style scoped lang="scss">
:deep(.el-icon) {
  color: var(--el-text-color-regular);
}
</style>
