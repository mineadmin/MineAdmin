<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://gitee.com/xmo/mineadmin-vue
-->
<script setup lang="ts">
import jsonData from './lib/cn.json'
import type { Area, ModelType } from './type.ts'

defineOptions({ name: 'MaProvinceCitySelect' })

const { mode = 'name', showLevel = 3 } = defineProps<{
  mode: 'name' | 'code'
  showLevel: 1 | 2 | 3
}>()

const model = defineModel<ModelType>()
const province = ref<Area>([])
const city = ref<Area>([])
const area = ref<Area>([])

function provinceChange(val, clear = true) {
  if (clear) {
    model.value.city = ''
    model.value.area = ''
    city.value = []
    area.value = []
  }
  city.value = jsonData.find((item: Area) => mode === 'name' ? item.name === val : item.code === val)?.children ?? []
}

function cityChange(val, clear = true) {
  if (clear) {
    model.value.area = ''
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
  <div class="grid grid-cols-3 gap-x-2" v-bind="$attrs">
    <el-select
      v-model="model.province"
      class="w-full"
      placeholder="请选择省/直辖市/自治区"
      clearable
      @change="provinceChange"
      @clear="
        () => {
          model.province = ''
          model.city = ''
          model.area = ''
        }
      "
    >
      <el-option v-for="item in province" :key="item" :label="item.name" :value="mode === 'name' ? item.name : item.code" />
    </el-select>
    <el-select
      v-if="showLevel >= 2"
      v-model="model.city"
      class="w-full"
      placeholder="请选择地级市/市辖区"
      clearable
      @change="cityChange"
      @clear="
        () => {
          model.city = ''
          model.area = ''
        }
      "
    >
      <el-option v-for="item in city" :key="item" :label="item.name" :value="mode === 'name' ? item.name : item.code" />
    </el-select>
    <el-select
      v-if="showLevel >= 3"
      v-model="model.area"
      class="w-full"
      placeholder="请选择区县"
      clearable
      @clear="
        () => {
          model.area = ''
        }
      "
    >
      <el-option v-for="item in area" :key="item" :label="item.name" :value="mode === 'name' ? item.name : item.code" />
    </el-select>
  </div>
</template>
