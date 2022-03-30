<template>
  <el-container>
    <el-aside width="240px" v-loading="showDirloading">
      <el-container>
        <el-header>
          <el-input placeholder="过滤类型" v-model="filterText" clearable></el-input>
        </el-header>
        <el-main class="nopadding">
          <el-tree
            ref="dirs"
            class="menu"
            node-key="name"
            :props="props"
            :load="loadNode"
            :data="fileType"
            :current-node-key="''"
            :highlight-current="true"
            :filter-node-method="filterNode"
            @node-click="typeClick"
          >
            <template #default="{ node }">
              <span class="custom-tree-node">
                <span class="label">{{ node.label }}</span>
              </span>
            </template>
          </el-tree>
        </el-main>
      </el-container>
    </el-aside>
    <el-container>
      <el-header>
        <div class="left-panel">

          <ma-resource-select :resource="false" @upload-data="handleSuccess" />

          <el-button-group>
            <el-button
              plain
              v-if="! isRecycle"
              icon="el-icon-delete"
              v-auth="['system:attachment:delete']"
              @click="batchDel"
            >删除附件</el-button>

            <el-button
              plain
              v-else
              icon="el-icon-refresh-left"
              v-auth="['system:attachment:recovery']"
              @click="recovery"
            >恢复附件</el-button>

            <el-tooltip class="item" effect="dark" content="选择当前页所有" placement="top">
              <el-button  icon="el-icon-check" @click="selectAll">全选</el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" content="反选当前页所有" placement="top">
              <el-button  icon="el-icon-minus" @click="selectInvert">反选</el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" content="清除所有选中的" placement="top">
              <el-button  icon="el-icon-refresh" @click="checkList = []">清除</el-button>
            </el-tooltip>
          </el-button-group>

        </div>
        <div class="right-panel">
          <div class="right-panel-search">
            <el-input v-model="queryParams.origin_name" clearable placeholder="请输入原文件名"></el-input>

            <el-tooltip class="item" effect="dark" content="搜索" placement="top">
              <el-button type="primary" icon="el-icon-search" @click="handlerSearch"></el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" content="清空条件" placement="top">
              <el-button icon="el-icon-refresh" @click="resetSearch"></el-button>
            </el-tooltip>

            <el-popover placement="bottom-end" :width="450" trigger="click" >
              <template #reference>
                <el-button type="text" @click="povpoerShow = ! povpoerShow">
                  更多筛选<i class="el-icon-arrow-down el-icon--right"></i>
                </el-button>
              </template>
              <el-form label-width="80px">

                <el-form-item label="存储模式" prop="status">
                  <el-select v-model="queryParams.storage_mode" clearable placeholder="请选择存储模式">
                    <el-option :label="item.label" :value="item.value" v-for="(item, index) in storageMode" :key="index">{{item.label}}</el-option>
                  </el-select>
                </el-form-item>

                <el-form-item label="创建时间">
                  <el-date-picker
                    clearable
                    v-model="dateRange"
                    type="daterange"
                    range-separator="至"
                    @change="handleDateChange"
                    value-format="YYYY-MM-DD"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期"
                  ></el-date-picker>
                </el-form-item>

              </el-form>
            </el-popover>
          </div>
        </div>
      </el-header>
      <el-main class="nopadding file" >
        <el-row class="file-list" v-loading="showFileloading" v-if="dataList.length > 0">
          <el-checkbox-group v-model="checkList">
            <ul class="el-upload-list el-upload-list--picture-card">
              <li
                v-for="(item, index) in dataList"
                :key="index"
                class="el-upload-list__item"
              >
                <div class="thumbnail">
                  <el-checkbox class="check" :label="item" > {{ index + 1 }}</el-checkbox>
                  <div class="mask" v-auth="['system:attachment:delete']">
                    <span class="del" @click.stop="deletes(item.id)">
                      <el-icon><el-icon-delete /></el-icon>
                    </span>
                  </div>
                  <div class="icon" v-if="item.mime_type && item.mime_type.indexOf('image') === -1">
                    <el-icon><el-icon-document /></el-icon>
                  </div>
                  <el-image v-else :src="viewImage(item.url)" fit="cover" :preview-src-list="preview" hide-on-click-modal preview-teleported />
                  <el-tooltip placement="bottom">
                    <div class="filename"> {{ item.origin_name }} </div>
                    <template #content>

                      原名称：
                      <span>{{ item.origin_name }}</span>
                      <br />

                      <span>存储名称：{{ item.object_name }}<br /></span>

                      <span>存储目录：{{ item.storage_path }}<br /></span>

                      上传日期：
                      <span>{{ item.created_at }}</span>
                      <br />

                      <span>大小：{{ item.size_info }}</span>
                    </template>
                    <div class="name">{{ item.origin_name }}</div>

                  </el-tooltip>
                </div>
              </li>
            </ul>
          </el-checkbox-group>
        </el-row>

        <el-empty class="file-list" v-else description="暂无数据" />

        <div class="scTable-page">
          <div class="scTable-pagination">
            <el-pagination
              background
              layout="prev, pager, next"
              :total="pageInfo.total"
              :page-size="queryParams.pageSize"
              v-model:currentPage="queryParams.page"
              @current-change="getList"
            ></el-pagination>
          </div>
          <div class="scTable-do">
            <el-tooltip class="item" effect="dark" :content="isRecycle ? '显示正常数据' : '显示回收站数据'" placement="left">
              <el-button
                @click="switchData"
                icon="el-icon-delete"
                circle
                style="margin-left:15px"
              ></el-button>
            </el-tooltip>
          </div>
        </div>
        
      </el-main>

    </el-container>

  </el-container>
</template>

<script>
  import mixin from './mixins/index'
  export default {
    name: 'system:attachment',
    mixins: [mixin],
  }
</script>

<style scoped lang="scss">
@import './style/index.scss';
.scTable-page {
  position: absolute; width: 100%; bottom: 0;
}
[data-theme='dark'] { 
  .el-upload-list--picture-card .el-upload-list__item {
    background: #585858;
  }
  .el-image__error {
    background: none;
    color: #fff;
  }
}
</style>
