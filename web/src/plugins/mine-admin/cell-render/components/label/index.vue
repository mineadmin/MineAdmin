<script lang="ts">
import { defineComponent } from 'vue'
import { cellRenderPluginName, createOptions, createRowFieldValues } from '../../utils/tools.ts'

export interface Options {
  map: Record<any, any>
}

export default defineComponent({
  name: cellRenderPluginName('label'),
  props: {
    field: {
      type: String,
    },
    scope: {
      type: Object,
      default: () => ({}),
    },
    options: {
      type: Object,
      default: () => ({
        map: {},
      }),
    },
  },
  setup(props) {
    const { value, row } = createRowFieldValues(props)
    const options = createOptions(props, {})
    const v = computed(() => {
      return options.value.map[value.value] ?? value.value
    })
    return () => typeof v.value === 'function' ? v.value(value.value, row.value, props.scope) : v.value
  },
})
</script>

<template>
</template>

<style scoped lang="scss">

</style>
