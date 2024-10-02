<script lang="tsx">
import type { PropType } from 'vue'
import { defineComponent } from 'vue'
import type { TableColumnScope } from '@mineadmin/table'
import { cellRenderPluginName, createOptions, createRowFieldValues, exec, getConfig } from '../../utils/tools.ts'

// 定义options类型,与ImageProps类型合并
// value.value, row.value, props.scope
export interface Options {
  buttons: {
    name?: string
    icon?: string | ((value: any, row: any, scope: TableColumnScope) => string)
    text?: string | ((value: any, row: any, scope: TableColumnScope) => string)
    disabled?: (value: any, row: any, scope: TableColumnScope) => boolean
    hidden?: (value: any, row: any, scope: TableColumnScope) => boolean
    confirm?: (value: any, row: any, scope: TableColumnScope) => string
    onClick?: (value: any, row: any, scope: TableColumnScope) => void
  }[]

}

export default defineComponent({
  name: cellRenderPluginName('buttons'),
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
    const { value, row } = createRowFieldValues(props)
    const options = createOptions(props, getConfig('buttons'))
    const loadingMap = ref({})
    const buttons = computed(() => {
      return options.value.buttons.map((button) => {
        return {
          ...button,
          loading: loadingMap.value[button.name] || false,
        }
      })
    })
    const bind = computed(() => {
      const {
        buttons,
        ...rest
      } = options.value
      return rest
    })

    const onClick = (button) => {
      console.log(props)
      loadingMap.value[button.name] = true
      exec(button.onClick, value.value, row.value, props.scope).then(() => {
        loadingMap.value[button.name] = false
      })
    }
    const renderButton = (button, bindOnClick = true) => (
      <el-button class="ma-cell-render-button" link type={button.type || 'primary'} loading={button.loading} onClick={bindOnClick ? () => onClick(button) : null}>
        {{
          icon: () => (<ma-svg-icon name={button.icon} />),
          default: () => (button.text),
        }}
      </el-button>
    )

    const getConfirmText = (button) => {
      // 如果是函数则执行函数
      return typeof button.confirm === 'function' ? button.confirm(value.value, row.value, props.scope) : button.confirm
    }

    return () => buttons.value.map((button) => {
      if (button.confirm) {
        return (
          <el-popconfirm title={getConfirmText(button)} onConfirm={() => onClick(button)}>
            {{ reference: () => renderButton(button, false) }}
          </el-popconfirm>
        )
      }
      return renderButton(button, true)
    })
  },
  computed: {
    defaultAvatar() {
      return defaultAvatar
    },
  },
})
</script>

<style lang="scss">
.ma-cell-render-button+.el-button {
  margin-left: 2px;
}
.ma-cell-render-button [class*=el-icon]+span {
  margin-left: 1px;
}
</style>
