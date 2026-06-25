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
import type { AppVo } from '../api/app.ts'
import { getDetail, unInstall } from '../api/app.ts'
import versionCompare from '../utils/versionCompare.ts'
import discount from '../utils/discount.ts'
import { isUndefined } from 'lodash-es'
import { useMessage } from '@/hooks/useMessage.ts'
import { MdPreview } from 'md-editor-v3'
import { ElMessageBox } from 'element-plus'
import '../style/preview.css'

const settingStore = useSettingStore()
const pluginStore = usePluginStore()
const dayjs = useDayjs(null, true) as any
const dataList = inject('dataList') as Record<string, any>
const msg = useMessage()
const dataState = ref<Record<string, boolean>>({
  visible: false,
  loading: false,
  buttonLoading: false,
  isInstall: false,
  isUpdated: false,
})

const textState = ref<Record<string, string>>({
  installBtnDefaultText: '下载并安装此应用',
  installButtonText: '下载并安装此应用',
  unInstallBtnText: '卸载此应用',
  updateBtnText: '更新应用',
})

const tabType = ref<string>('desc')
const data = ref<AppVo>({
  version: [],
})

function open(identifier: string) {
  dataState.value.visible = true
  dataState.value.loading = true
  dataState.value.buttonLoading = false
  getDetail({ identifier }).then((res: any) => {
    dataState.value.loading = false
    data.value = res.data.data
    const key = `${data.value?.app?.identifier}`
    if (checkInstallStatus(key)) {
      dataState.value.isInstall = true
    }
    if (dataList.value.local[key]) {
      if (versionCompare(dataList.value.local[key]?.version, data.value?.version?.[0]!.version as string) < 0) {
        dataState.value.isUpdated = true
      }
    }
  })
}

function checkInstallStatus(name: string) {
  return !isUndefined(dataList.value.local[name]) && dataList.value.local[name].status
}

function openPage() {
  window.open(`https://www.mineadmin.com/store/${data.value?.app?.identifier.replace('/', '~')}`)
}

function downloadAndInstall() {
  if (!data.value) {
    msg.error('获取应用信息失败，无法安装，重新打开应用详情获取数据后再试')
    return false
  }

  const md = [
    '```bash',
    '# 进入到后端根目录，第一步下载应用',
    `php bin/hyperf.php mine-extension:download ${data.value?.app?.identifier}`,
    '',
    '# 第二步安装应用',
    `php bin/hyperf.php mine-extension:install ${data.value?.app?.identifier}`,
    '```',
  ].join('\n')

  ElMessageBox({
    title: '⚠️ 温馨提示',
    showConfirmButton: false,
    message: () =>
      h(MdPreview, {
        modelValue: md,
        codeFoldable: false,
        theme: settingStore.colorMode === 'dark' ? 'dark' : 'light',
        previewTheme: 'github',
      }),
  })
}

function unInstallApp() {
  if (!data.value) {
    msg.error('获取应用信息失败，无法卸载，重新打开应用详情获取数据后再试')
    return false
  }

  const body = {
    identifier: data.value.app?.identifier,
    version: data.value?.version?.[0]!.version,
  }

  dataState.value.buttonLoading = true
  textState.value.unInstallBtnText = '应用卸载中...'
  unInstall(body).then((_) => {
    msg.success('应用卸载成功')
    pluginStore.disabled(data.value.app?.identifier)
    dataState.value.buttonLoading = false
    dataState.value.isInstall = true
  }).catch((e) => {
    msg.alertError(e)
  })
}

function updatedApp() {
  if (!data.value) {
    msg.error('获取应用信息失败，无法更新，重新打开应用详情获取数据后再试')
    return false
  }
}

defineExpose({ open })
</script>

<template>
  <el-drawer
    v-model="dataState.visible"
    :title="data?.app?.name ?? '获取中...'"
    append-to-body
    size="1000"
  >
    <div v-loading="dataState.loading" class="w-full">
      <div class="flex gap-5">
        <div class="w-7/12">
          <el-carousel
            class="h-300px rounded"
            :auto-play="true"
            indicator-type="line"
            show-arrow="hover"
          >
            <el-carousel-item v-for="(image, idx) in data?.app?.homepage" :key="idx">
              <el-image
                :src="image"
                fit="contain"
                class="h-full w-full"
              />
            </el-carousel-item>
          </el-carousel>
          <el-space class="mt-2">
            <el-tag v-for="tag in data?.tags" :key="tag.name" typ="primary">
              {{ tag.name }}
            </el-tag>
          </el-space>
        </div>
        <div>
          <div class="text-base font-bold">
            {{ data?.app?.name }}
          </div>

          <el-descriptions :column="1" bordered class="mt-3">
            <el-descriptions-item label="应用价格">
              <el-tag v-if="data.auth?.type === 0" type="primary">
                免费
              </el-tag>
              <div v-else-if="data?.auth?.type === 1">
                <div class="flex items-center leading-6">
                  <div v-if="data?.auth.integral_discount !== '0.00'" class="text-gray-400 line-through">
                    {{ data?.auth.integral_quota }} 积分
                  </div>
                  <div class="text-emerald-700">
                    {{ discount(data?.auth.integral_discount as string, Number(data.auth?.integral_quota)) }} 积分
                  </div>
                </div>
              </div>
              <div v-else-if="data?.auth?.type === 2" class="flex items-center gap-x-3">
                <span v-if="data?.auth.basic_discount !== '0.00'" class="text-gray-400 line-through">
                  ￥{{ data?.auth.basic_quota }}
                </span>
                <span class="text-blue-500">
                  ￥{{ discount(data?.auth.basic_discount as string, Number(data?.auth?.basic_quota)) }}
                </span>
              </div>
            </el-descriptions-item>
            <el-descriptions-item v-if="data?.auth?.type === 2" label="授权方式">
              一年 / 永久
            </el-descriptions-item>
            <el-descriptions-item label="应用标识">
              {{ data?.app?.identifier }}
            </el-descriptions-item>
            <el-descriptions-item label="发布日期">
              {{ dayjs(data?.app?.created_at).format("YYYY-MM-DD") }}
            </el-descriptions-item>
            <el-descriptions-item label="更新日期">
              {{ `${dayjs(data?.version?.[0]?.created_at ?? '').fromNow()}更新` }}
            </el-descriptions-item>
            <el-descriptions-item label="下载次数">
              {{ data?.app?.download_count }}
            </el-descriptions-item>
          </el-descriptions>

          <el-button
            v-if="dataList.my.includes(data?.app?.identifier) && !dataState.isInstall"
            :loading="dataState.buttonLoading"
            class="mt-4"
            type="primary"
            @click="downloadAndInstall"
          >
            {{ textState.installButtonText }}
          </el-button>
          <el-space
            v-else-if="dataList.my.includes(data?.app?.identifier) && dataState.isInstall"
          >
            <el-button
              class="mt-4"
              type="primary"
              status="danger"
              :loading="dataState.buttonLoading"
              @click="unInstallApp"
            >
              {{ textState.unInstallBtnText }}
            </el-button>
            <el-button
              v-if="dataState.isUpdated"
              class="mt-4"
              type="primary"
              status="success"
              :loading="dataState.buttonLoading"
              @click="updatedApp"
            >
              {{ textState.updateBtnText }}
            </el-button>
          </el-space>
          <el-button
            v-else
            class="mt-4"
            type="primary"
            status="success"
            @click="openPage"
          >
            购买此应用
          </el-button>
        </div>
      </div>

      <m-tabs
        v-model="tabType"
        class="mt-5 text-sm"
        :options="[
          { label: '应用介绍', value: 'desc' },
          { label: '版本更新记录', value: 'versionRecord' },
        ]"
      />
      <div class="flex">
        <div class="w-9/12 pt-5">
          <div v-if="tabType === 'desc'" class="pr-5">
            <MdPreview
              id="description"
              :model-value="data?.version?.[0]?.version_desc"
              :theme="settingStore.colorMode === 'dark' ? 'dark' : 'light'"
              preview-theme="github"
            />
          </div>
          <template v-if="tabType === 'versionRecord'">
            <div v-for="(item, id) in data?.version" class="my-5 block justify-between lg:flex">
              <div class="h-auto w-full lg:w-2/12">
                <div class="text-xl font-semibold">
                  {{ item.version }}
                </div>
                <div class="mt-2 text-xs text-gray-500 space-y-0.5 dark:text-gray-300">
                  <div>{{ (data?.created_by as any)?.nickname ?? (data?.created_by as any)?.username }}</div>
                  <div>发布于 {{ `${dayjs(item?.created_at ?? '').fromNow()}` }}</div>
                </div>
              </div>
              <div class="mt-5 w-full b-1 border b-gray-2 rounded b-solid p-3 lg:mt-0 lg:w-10/12 dark:b-dark-3">
                <div class="flex justify-between b-b-1 b-b-gray-2 b-b-solid pb-2 dark:b-dark-3">
                  <div class="flex">
                    <div class="text-3xl font-semibold">
                      {{ item.version }}
                    </div>
                    <el-tag v-if="id === 0" class="ml-3 mt-2 h-5" type="info">
                      最新版
                    </el-tag>
                  </div>
                  <div class="mt-2 text-sm text-dark-8 dark:text-gray-3">
                    >= {{ item.require }}
                  </div>
                </div>

                <MdPreview
                  :id="`version_${id}`"
                  class="mt-2"
                  :model-value="item.update_log"
                  :theme="settingStore.colorMode === 'dark' ? 'dark' : 'light'"
                  preview-theme="github"
                />
                <!--              <div v-if="dataList.my.includes(data?.app?.identifier)" class="border-t pt-2 dark:border-gray-700"> -->
                <!--                <el-button> -->
                <!--                  安装此版本 -->
                <!--                </el-button> -->
                <!--              </div> -->
              </div>
            </div>
          </template>
        </div>
        <div class="ml-3 mt-5.5 w-3/12">
          <div class="flex">
            <el-avatar
              class="h-12 w-12 rounded-full"
              :src="(data?.created_by as any)?.avatar === '' ? undefined : (data?.created_by as any)?.avatar"
            >
              {{ (data?.created_by as any)?.username?.substring(0, 1).toUpperCase() }}
            </el-avatar>
            <div class="ml-3 mt-0.5 text-sm leading-6">
              <div class="text-gray-800 dark:text-white">
                {{ (data?.created_by as any)?.nickname ?? (data?.created_by as any)?.username }}
              </div>
              <div class="text-xs text-gray-500 dark:text-gray-400">
                应用开发者
              </div>
            </div>
          </div>
          <div class="w-full">
            <el-descriptions :column="1" direction="vertical" bordered class="mt-3">
              <el-descriptions-item label="最新版本">
                <el-text>
                  {{ data?.app?.desc.version ?? '-' }}
                </el-text>
              </el-descriptions-item>
              <el-descriptions-item>
                <el-divider class="my-1" />
              </el-descriptions-item>
              <el-descriptions-item label="兼容版本">
                <el-text>
                  支持 >= {{ data?.app?.desc.require ?? '-' }} 版本
                </el-text>
              </el-descriptions-item>
              <el-descriptions-item>
                <el-divider class="my-1" />
              </el-descriptions-item>
              <el-descriptions-item label="在线演示">
                <el-link
                  v-if="data?.app?.desc.preview_url"
                  type="primary"
                  :href="data.app.desc.preview_url"
                  target="_blank"
                >
                  点击跳转
                </el-link>
                <el-text v-else>
                  暂无
                </el-text>
              </el-descriptions-item>
            </el-descriptions>
          </div>
        </div>
      </div>
    </div>
  </el-drawer>
</template>

<style scoped lang="scss">
:deep(.el-descriptions__cell) {
  @apply flex;
}
:deep(.md-editor-code-head) {
  width: 100%; display: flex;
}
:deep(.md-editor-code-lang) {
  @apply hidden;
}
:deep(.md-editor-code-action) {
  @apply w-full flex gap-x-3;
}
</style>
