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
import Message from 'vue-m-message'
import http from '@/utils/http'

interface wordDataType {
  content?: string
  origin?: string
}

const { createHttp } = http
const wordData = ref<wordDataType>({})

function requestOneWord() {
  createHttp('https://api.xygeng.cn/openapi/one').get('/').then((res: any) => {
    if (res.status === 200 && res.data.data) {
      wordData.value = res.data.data
    }
  }).catch((err) => {
    Message.warning('读取每日一言失败', err)
  })
}

function selectText(e: any) {
  const selection: Selection | null = window.getSelection()
  const range: Range = document.createRange()
  range.selectNodeContents(e.target)
  if (selection) {
    selection.removeAllRanges()
    selection.addRange(range)
  }
}

onMounted(() => {
  requestOneWord()
})
</script>

<template>
  <div class="group absolute bottom-5 left-10 z-10 w-[85%] text-white">
    <div class="text-sm" @click="selectText">
      {{ wordData.content }}
    </div>
    <div class="mt-5 flex items-center gap-x-2 text-left text-[12px]">
      <p>
        -- {{ wordData.origin }}
      </p>
      <div class="hidden group-hover:block group-hover:animate-fade-in group-hover:animate-duration-100">
        <ma-svg-icon
          name="iconoir:refresh"
          class="relative top-[3px] cursor-pointer text-[13px]"
          @click="requestOneWord"
        />
      </div>
    </div>
  </div>
</template>
