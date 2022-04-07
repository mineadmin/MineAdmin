<template>
  <div class="editor" ref="dom" :style="'width: 100%; height: ' + props.height + 'px'"></div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api.js'
// import 'monaco-editor/esm/vs/language/json/monaco.contribution';
import 'monaco-editor/esm/vs/basic-languages/javascript/javascript.contribution'
import 'monaco-editor/esm/vs/editor/contrib/find/findController.js'

const props = defineProps({
  modelValue: {
    type: String,
    default: () => {}
  },
  height: {
    type: Number,
    default: 300
  },
  theme: {
    type: String,
    default: 'vs'
  }
})

const emit = defineEmits(['update:modelValue'])
const dom = ref()

let instance

onMounted(() => {
  instance = monaco.editor.create(dom.value, {
    model: monaco.editor.createModel(props.modelValue, 'javascript'),
    tabSize: 2,
    automaticLayout: true,
    scrollBeyondLastLine: false,
    language:"javascript",
    theme: props.theme,
    autoIndent: true,
    minimap: { enabled: false }
  })

  instance.onDidChangeModelContent(() => {
    emit('update:modelValue', instance.getValue())
  })
})
</script>

<style scoped lang="scss">
.editor {
  border: var(--el-input-border, var(--el-border-base));
  border-radius: var(--el-input-border-radius,var(--el-border-radius-base));
  background: var(--el-input-background-color,var(--el-color-white));
}
</style>