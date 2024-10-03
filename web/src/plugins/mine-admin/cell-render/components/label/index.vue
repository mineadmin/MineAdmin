<script lang="ts">
import { defineComponent } from 'vue'
import { cellRenderPluginName, cellRenderPluginProps, createOptions, organizeProps } from '../../utils/tools.ts'

export interface Options {
  map: Record<any, any>
}

export default defineComponent({
  name: cellRenderPluginName('label'),
  props: cellRenderPluginProps(),
  setup(props) {
    const { value, row } = organizeProps(props)
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
