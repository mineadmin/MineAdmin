<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import { VAceEditor } from "vue3-ace-editor";
import 'ace-builds/src-noconflict/mode-json'
import 'ace-builds/src-noconflict/theme-dawn'
import 'ace-builds/src-noconflict/theme-github_dark'
import { useColorMode } from '@vueuse/core'
import formatJson from '../utils/formatJson.ts'

defineOptions({ name: 'system:group:form' })

const color = useColorMode()
const t = useTrans().globalTrans
const convertArray = ref()
const content = ref();

// Watch for changes in content and format it
watch(content, (newValue) => {
  try {
    const parsedJson = JSON.parse(newValue);
    content.value = formatJson(parsedJson); // Format the JSON
  } catch (error) {
    // Handle invalid JSON format if necessary
  }
});

const theme = computed(() => color.value === 'dark' ? 'github_dark' : 'dawn')

// ok事件
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    try {
      // 将 JSON 字符串转换为数组
      const parsedArray = JSON.parse(content.value)

      // 如果转换成功，并且是数组，则返回成功的响应
      if (Array.isArray(parsedArray)) {
        resolve({
          code: 200,
          success: true,
          message: 'success',
          data: parsedArray,
        })
        convertArray.value = parsedArray
      } else {
        reject({
          code: 404,
          success: false,
          message: 'Parsed content is not an array',
        })
      }
    } catch (error) {
      reject({
        code: 404,
        success: false,
        message: 'Invalid JSON format',
      })
    }
  })
}

defineExpose({
  add,
  maForm: convertArray,
})

</script>

<template>
  <VAceEditor v-model:value="content" lang="json" :theme="theme" class="mt-2 h-500px text-base" />
</template>

<style scoped lang="scss"></style>
