<script lang="tsx">
import type { PropType } from 'vue'
import { defineComponent } from 'vue'
import type { SwitchEmits, SwitchProps } from 'element-plus'
import type { WithOnEventListeners } from '../../utils/tools.ts'
import { cellRenderPluginName, createOptions, createRowFieldValues, getConfig } from '../../utils/tools.ts'
import { useMessage } from '@/hooks/useMessage.ts'

export interface Emits extends SwitchEmits {
}

// 定义options类型,与ImageProps类型合并
export interface Options extends Omit<Partial<SwitchProps>, 'loading'>, WithOnEventListeners<Emits> {
  api: ((data) => Promise<any>) | string
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

    const beforeChange = () => {
      loading.value = true
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
        ...rest
      } = options.value
      return rest
    })

    return () => (
      <el-switch loading={loading.value} model-value={value.value} before-change={beforeChange} {...bind.value}></el-switch>
    )
  },
})
</script>
