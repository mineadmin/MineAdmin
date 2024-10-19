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
import ReportOverview from './components/report/report-overview.vue'
import ReportPublishSource from './components/report/report-publish-source.vue'
import ReportUserAction from './components/report/report-user-action.vue'
import ReportContentClassify from './components/report/report-content-classify.vue'
import ReportItems from './components/report/report-items.vue'

defineOptions({ name: 'dashboard:report' })
</script>

<template>
  <div class="mine-layout">
    <div class="lg:flex">
      <ReportOverview />
      <div class="w-full lg:w-4/12">
        <ReportUserAction />
        <ReportContentClassify />
      </div>
    </div>

    <div class="mine-layout grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4">
      <ReportItems title="用户留存趋势" quota="retentionTrends" chart-type="line" />
      <ReportItems title="用户留存量" quota="userRetention" chart-type="bar" class="!ml-3 !lg:ml-0" />
      <ReportItems title="内容消费趋势" quota="contentConsumptionTrends" chart-type="line" class="!ml-3 !lg:ml-0" />
      <ReportItems title="内容消费量" quota="contentConsumption" chart-type="bar" class="!ml-3 !lg:ml-0" />
    </div>

    <ReportPublishSource />
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
