<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { MaTableExpose } from '@mineadmin/table'
import useTable from '@/hooks/useTable.ts'

defineOptions({ name: 'welcome' })
const userinfo = useUserStore().getUserInfo()

useTable('table').then((table: MaTableExpose) => {
  table.setColumns([
    { label: '成员', prop: 'member', width: 150, align: 'center' },
    { label: '动态', prop: 'dynamic', align: 'center' },
    { label: '时间', prop: 'timer', width: 180, align: 'center',
      cellRender: ({ row }) => {
        return useDayjs(row.timer).fromNow()
      },
    },
  ])

  table.setData([
    { member: 'IT界-风清扬', dynamic: '上班不摸鱼，与咸鱼有什么区别。', timer: '2024-09-25 17:10:20' },
    { member: 'Anna', dynamic: '技术部那几位童鞋，再次警告，不要摸鱼，不要摸鱼，不要摸鱼啦！', timer: '2024-09-15 16:34:56' },
    { member: '小李子', dynamic: '中午吃什么嘞，好烦呐！那个谁，来一段唱、跳、rap和篮球吧', timer: '2024-09-09 12:01:10' },
    { member: '咩咩羊', dynamic: '向 MineAdmin 提交了一个bug，抽时间看看吧！', timer: '2024-09-05 14:20:07' },
    { member: '李大', dynamic: '刚才把工作台页面随便写了一些，凑合能看了！', timer: '2024-09-03 10:43:36' },
  ])
})
</script>

<template>
  <div class="mine-layout">
    <div class="flex justify-between bg-white p-3 dark-bg-dark-8">
      <div class="flex gap-x-5">
        <el-avatar :src="userinfo?.avatar" :size="80">
          <span v-if="!userinfo?.avatar" class="text-5xl">{{ userinfo.username[0].toUpperCase() }}</span>
        </el-avatar>
        <div class="flex flex-col justify-center gap-y-2">
          <span class="text-xl">早安，天青色等烟雨，而我在等你！</span>
          <span class="text-sm text-dark-1 dark-text-gray-3">某某公司 - 某某部门 - 技术总监</span>
        </div>
      </div>
    </div>

    <div class="justify-between lg:flex">
      <div class="mine-card w-auto lg:w-8/12">
        <div class="text-base">
          <div>进行中的项目</div>
        </div>
        <div class="grid grid-cols-1 mt-3 lg:grid-cols-3">
          <div class="run-list">
            <div class="flex items-center gap-x-3">
              <ma-svg-icon name="skill-icons:php-light" :size="30" />
              <div>
                Hypertext Preprocessor
              </div>
            </div>
            <div class="desc">
              即“超文本预处理器”，是在服务器端执行的脚本语言，尤其适用于Web开发并可嵌入HTML中。
            </div>
          </div>
          <div class="run-list">
            <div class="flex items-center gap-x-3">
              <ma-svg-icon name="ion:logo-javascript" :size="30" />
              <div>
                Javascript
              </div>
            </div>
            <div class="desc">
              JavaScript基于原型编程、多范式的动态脚本语言，并且支持面向对象、命令式、声明式、函数式编程范式。
            </div>
          </div>
          <div class="run-list !b-r-0">
            <div class="flex items-center gap-x-3">
              <ma-svg-icon name="akar-icons:github-fill" :size="30" />
              <div>
                Github
              </div>
            </div>
            <div class="desc">
              GitHub是一个面向开源及私有软件项目的托管平台，因为只支持Git作为唯一的版本库格式进行托管，故名GitHub。
            </div>
          </div>
          <div class="run-list !lg:b-b-0">
            <div class="flex items-center gap-x-3">
              <ma-svg-icon name="skill-icons:phpstorm-light" :size="30" />
              <div>
                PHP Storm
              </div>
            </div>
            <div class="desc">
              JetBrains 公司开发的一款商业的PHP集成开发工具，可深刻理解用户的编码，提供智能代码补全、即时错误检查。
            </div>
          </div>
          <div class="run-list !lg:b-b-0">
            <div class="flex items-center gap-x-3">
              <ma-svg-icon name="skill-icons:webstorm-light" :size="30" />
              <div>
                Web Storm
              </div>
            </div>
            <div class="desc">
              JetBrains 公司旗下一款 JavaScript 开发工具。已经被广大中国JS开发者誉为“Web前端开发神器”。
            </div>
          </div>
          <div class="run-list !b-r-0 !lg:b-b-0">
            <div class="flex items-center gap-x-3">
              <ma-svg-icon name="logos:chrome" :size="30" />
              <div>
                Google Chrome
              </div>
            </div>
            <div class="desc">
              该浏览器基于其他开源软件 WebKit 编写，目标是提升稳定、速度和安全性，并提供简单的使用者界面。
            </div>
          </div>
        </div>
      </div>
      <div class="mine-card w-auto !ml-3 lg:w-4/12 !lg:ml-0">
        <div class="text-base">
          <div>MineAdmin 入门及开发</div>
        </div>
        <div class="mt-3 p-2 text-sm leading-6">
          感谢选择 MineAdmin 作为您的项目开发脚手架，我们为您提供了入门路径、二次开发项目指南以及现成的应用插件，
          我们致力于：<el-text type="primary">
            运用技术，为公司和品牌创造卓越的价值。
          </el-text>
        </div>
        <div class="p-2 text-sm text-gray-5 dark-text-[#ccc]">
          <ul class="ma-link">
            <li>
              官方网站：<el-link target="_blank" href="https://www.mineadmin.com">
                https://www.mineadmin.com
              </el-link>
            </li>
            <li>
              开发文档：<el-link target="_blank" href="https://doc.mineadmin.com">
                https://doc.mineadmin.com
              </el-link>
            </li>
            <li>
              应用市场：<el-link target="_blank" href="https://www.mineadmin.com/store">
                https://www.mineadmin.com/store
              </el-link>
            </li>
            <li>
              QQ交流群：<el-link target="_blank" href="https://qm.qq.com/cgi-bin/qm/qr?k=Uq4VW1H9jtDhEKsUb3hfjHraiSG80FI4&jump_from=webapi&authKey=bpaCvnQ65RpLdyQx8m57iQNc9OtgJgyIjrcG3qDrJZhnL4QdqzDLLQS8fx5jkevE">
                150105478，点击加入
              </el-link>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="mine-card">
      <div class="text-base">
        <div>部门动态</div>
      </div>
      <ma-table ref="table" class="mt-5" />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.run-list {
  @apply b-1 b-solid b-gray-1 dark-b-dark-3 p-3 b-l-0 b-t-0 b-r-0 lg:b-r-1
  transition-all duration-300
  hover-shadow dark-hover-shadow-dark-3
  ;

  .desc {
    @apply mt-3 text-sm leading-6 dark-text-[#888] text-gray-5
  }
}

.ma-link li {
  @apply flex items-center py-1.5;
}
</style>
