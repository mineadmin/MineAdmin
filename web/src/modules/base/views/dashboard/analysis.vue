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
import AnalysisItem from './components/analysis/analysis-item.vue'
import AnalysisContentPublish from './components/analysis/analysis-content-publish.vue'
import AnalysisHotAuthor from './components/analysis/analysis-hot-author.vue'
import AnalysisContentTimer from './components/analysis/analysis-content-timer.vue'

defineOptions({ name: 'dashboard:analysis' })
</script>

<template>
  <div class="mine-layout">
    <div class="mine-card">
      <div class="text-base">
        <div>舆情分析</div>
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div><AnalysisItem name="visitors" chart-type="line" title="访问总人次" /></div>
        <div><AnalysisItem name="published" chart-type="bar" title="内容发布量" /></div>
        <div><AnalysisItem name="visitors" chart-type="line" title="评论总量" /></div>
        <div><AnalysisItem name="contentTimer" chart-type="pie" title="分享总量" /></div>
      </div>
    </div>

    <div class="xl:flex">
      <AnalysisContentPublish />
      <AnalysisHotAuthor />
    </div>

    <AnalysisContentTimer />
  </div>
</template>

<style>
.echarts-tooltip-diy {
  background: linear-gradient(
    304.17deg,
    rgba(253, 254, 255, 0.6) -6.04%,
    rgba(244, 247, 252, 0.6) 85.2%
  ) !important;
  border: none !important;
  backdrop-filter: blur(10px) !important;
  /* Note: backdrop-filter has minimal browser support */

  border-radius: 6px !important;
  .content-panel {
    display: flex;
    justify-content: space-between;
    padding: 0 9px;
    background: rgba(255, 255, 255, 0.8);
    width: 164px;
    height: 32px;
    line-height: 32px;
    box-shadow: 6px 0px 20px rgba(34, 87, 188, 0.1);
    border-radius: 4px;
    margin-bottom: 4px;
  }
  .tooltip-title {
    margin: 0 0 10px 0;
  }
  p {
    margin: 0;
  }
  .tooltip-title,
  .tooltip-value {
    font-size: 13px;
    line-height: 15px;
    display: flex;
    align-items: center;
    text-align: right;
    color: #1d2129;
    font-weight: bold;
  }
  .tooltip-item-icon {
    display: inline-block;
    margin-right: 8px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
  }
}
</style>
