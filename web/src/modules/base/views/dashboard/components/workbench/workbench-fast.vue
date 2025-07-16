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
const t = useTrans().globalTrans
const router = useRouter()

const visibleRoutes = computed(() =>
  router.getRoutes()
    .filter((v: any) => /^(?!\/$)(?!.*\/uc)(?!.*\/login)(?!.*\/:pathMatch\([^)]*\)).*$/.test(v.path) && v.components)
    .slice(0, 12)
)
</script>

<template>
  <div class="lg:flex">
    <div class="mine-card w-auto lg:w-8/12">
      <div class="text-base">
        快捷入口
      </div>
      <div class="grid grid-cols-3 mt-3 gap-3 lg:grid-cols-4 md:grid-cols-4 xl:grid-cols-6">
        <div v-for="item in visibleRoutes" :key="item.name" class="flex-center">
          <el-link underline="never" @click="() => router.push(item.path)">
            <div class="link">
              <ma-svg-icon :name="(item.meta?.icon ?? 'i-carbon:unknown') as string" :size="26" />
              {{ item.meta?.i18n ? t(item.meta.i18n) : item.meta?.title ?? 'unknown' }}
            </div>
          </el-link>
        </div>
      </div>
    </div>
    <div class="mine-card w-auto !ml-3 lg:w-4/12 !lg:ml-0">
      <el-carousel height="230px" class="w-full rounded">
        <el-carousel-item v-for="(item, index) in Array.from({ length: 5 })" :key="item">
          <img :src="`https://picsum.photos/600/240?random=${index + 1}`" :alt="index" class="h-full w-full rounded object-cover">
        </el-carousel-item>
      </el-carousel>
    </div>
  </div>
</template>

<style scoped lang="scss">
.link {
  transition: all .15s;
  @apply min-w-20 flex flex-col items-center gap-y-2 rounded p-4
  hover-bg-[rgb(var(--ui-primary)/10%)]
  ;
}
</style>
