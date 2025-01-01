<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://gitee.com/xmo/mineadmin-vue
-->
<i18n lang="yaml">
en:
  province: Please select province/city/area
  city: Please select city/area
  area: Please select area
zh_CN:
  province: 请选择省/直辖市/自治区
  city: 请选择地级市/市辖区
  area: 请选择区县
zh_TW:
  province: 請選擇省/直轄市/自治區
  city: 請選擇地級市/市轄區
  area: 請選擇區縣
</i18n>
<script setup lang="ts">
import jsonData from './lib/cn.json'
import type { Area, ModelType } from './type.ts'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MaCitySelect' })

const { mode = 'name', showLevel = 3 } = defineProps<{
  mode: 'name' | 'code'
  showLevel: 1 | 2 | 3
}>()

const t = useLocalTrans()
const model = defineModel<ModelType>({ province: undefined, city: undefined, area: undefined})
const province = ref<Area>([])
const city = ref<Area>([])
const area = ref<Area>([])

function provinceChange(val, clear = true) {
  if (clear) {
    model.value.city = undefined
    model.value.area = undefined
    city.value = []
    area.value = []
  }
  city.value = jsonData.find((item: Area) => mode === 'name' ? item.name === val : item.code === val)?.children ?? []
}

function cityChange(val, clear = true) {
  if (clear) {
    model.value.area = undefined
    area.value = []
  }
  area.value = city.value.find((item: Area) => mode === 'name' ? item.name === val : item.code === val)?.children ?? []
}

onMounted(() => {
  jsonData.map((item: Area) => province.value.push(item))

  city.value = jsonData.find(
    (item: Area) => mode === 'name' ? item.name === model.value.province : item.code === model.value.province,
  )?.children ?? []

  area.value = city.value.find(
    (item: Area) => mode === 'name' ? item.name === model.value.city : item.code === model.value.city,
  )?.children ?? []
})
</script>

<template>
  <div class="grid grid-cols-3 gap-x-2 w-full" v-bind="$attrs">
    <el-select
      v-model="model.province"
      class="w-full"
      :placeholder="t('province')"
      clearable
      @change="provinceChange"
      @clear="
        () => {
          model.province = undefined
          model.city = undefined
          model.area = undefined
        }
      "
    >
      <el-option v-for="item in province" :key="item" :label="item.name" :value="mode === 'name' ? item.name : item.code" />
    </el-select>
    <el-select
      v-if="showLevel >= 2"
      v-model="model.city"
      class="w-full"
      :placeholder="t('city')"
      clearable
      @change="cityChange"
      @clear="
        () => {
          model.city = undefined
          model.area = undefined
        }
      "
    >
      <el-option v-for="item in city" :key="item" :label="item.name" :value="mode === 'name' ? item.name : item.code" />
    </el-select>
    <el-select
      v-if="showLevel >= 3"
      v-model="model.area"
      class="w-full"
      :placeholder="t('area')"
      clearable
      @clear="
        () => {
          model.area = undefined
        }
      "
    >
      <el-option v-for="item in area" :key="item" :label="item.name" :value="mode === 'name' ? item.name : item.code" />
    </el-select>
  </div>
</template>
