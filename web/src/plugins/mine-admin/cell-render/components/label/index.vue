<script lang="tsx">
import { defineComponent } from 'vue'
import { makeRFV } from '../../utils/tools.ts'

export interface Options {
  map: Record<any, any>
}

export default defineComponent({
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
    const { row, field, value } = makeRFV(props)
    return {
      row,
      field,
      value,
    }
  },
  render() {
    console.log(333)
    const { options, value } = this
    return options.map[value]
  },
})
</script>
