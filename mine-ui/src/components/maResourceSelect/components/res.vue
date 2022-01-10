<template>
    <el-dialog :title="title" v-model="resDialog" width="753px" :before-close="handleResClose">

      <!-- 按钮组 及 搜索 -->
      <el-row>

        <el-col :span="10">
          <el-button-group>

            <el-tooltip class="item" effect="dark" :content="'选择当前页所有' + (type == 'image' ? '图片' : '文件')" placement="top">
              <el-button size="small" icon="el-icon-check" @click="selectAll">全选</el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" :content="'反选当前页所有' + (type == 'image' ? '图片' : '文件')" placement="top">
              <el-button size="small" icon="el-icon-minus" @click="selectInvert">反选</el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" :content="'取消选择当前页' + (type == 'image' ? '图片' : '文件')" placement="top">
              <el-button size="small" icon="el-icon-close" @click="selectCancel">取消</el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" :content="'清除所有选中的' + (type == 'image' ? '图片' : '文件')" placement="top">
              <el-button size="small" icon="el-icon-refresh" @click="checkList = []">清除</el-button>
            </el-tooltip>

          </el-button-group>
        </el-col>

        <el-col :span="14">

          <el-input
            :placeholder="'输入' + (type == 'image' ? '图片' : '文件') + '名称筛选'"
            size="small"
            clearable
            v-model="queryParams.origin_name">
            <template #append>
                <el-button icon="el-icon-search" @click="getList()"></el-button>
            </template>
          </el-input>

        </el-col>

      </el-row>

      <el-row>
        <el-breadcrumb separator-class="el-icon-arrow-right" class="breadcrumb" style="width: 100%">
          <!-- @click="loadPath(item.path)" -->
          <el-breadcrumb-item v-for="(item, index) in breadcrumb" :key="index">
            <a @click="openFolder(item.path, 'out')">{{ item.name }}</a>
          </el-breadcrumb-item>

        </el-breadcrumb>
      </el-row>

      <el-row class="file-list" v-if="dataList.length > 0" style="margin-top: 20px;">
        <el-checkbox-group v-model="checkList">
          <div class="file-list">
            <div class="list" v-for="(item, index) in dataList" :key="index">

              <div class="icon" @click="openFolder(item.basename, 'in')" v-if="item.type === 'dir'">
                <el-icon><el-icon-folder /></el-icon>
              </div>

              <div class="icon" v-if="item.mime_type && item.mime_type.indexOf('image') === -1">
                <el-icon><el-icon-document /></el-icon>
              </div>

              <div class="file" v-if="item.mime_type && item.mime_type.indexOf('image') > -1">
                <el-checkbox class="check" :label="item" > {{ index + 1 }}</el-checkbox>
                <el-image class="image" :src="viewImage(item.url)" fit="contain" @click="selectAdd(item)"></el-image>
              </div>

              <el-tooltip placement="bottom">

                <template #content>
                  <span v-if="item.type === 'dir'">文件夹<br /></span>

                  原名称：
                  <span v-if="item.type === 'dir'">{{ item.basename }}</span>
                  <span v-else>{{ item.origin_name }}</span>
                  <br />

                  <span v-if="item.type !== 'dir'">存储名称：{{ item.object_name }}<br /></span>

                  日期：
                  <span v-if="item.type === 'dir'">{{ dayjs(item.timestamp * 1000).format('YYYY-MM-DD HH:mm:ss') }}</span>
                  <span v-else>{{ item.created_at }}</span>
                  <br />

                  <span v-if="item.type !== 'dir'">大小：{{ item.size_info }}</span>
                </template>

                <div class="name" v-if="item.type === 'dir'">{{ item.basename }}</div>
                <div class="name" v-else>{{ item.origin_name }}</div>

              </el-tooltip>
            </div>
          </div>
        </el-checkbox-group>
      </el-row>

      <el-empty v-else :description="'暂无' + (type == 'image' ? '图片' : '文件')">
        <el-button icon="el-icon-refresh" size="small" type="primary" @click="getList()">刷新</el-button>
      </el-empty>

      <template #footer class="dialog-footer">
        <el-pagination
          style="float:left;"
          small
          background
          layout="prev, pager, next"
          :total="pageInfo.total"
          :page-size="queryParams.pageSize"
          @size-change="getList"
          v-model:currentPage="queryParams.page"
          @current-change="getList" ></el-pagination>
        
        <el-button @click="handleResClose" size="small">
          关 闭
        </el-button>

        <el-button type="primary" @click="selectSubmit" :loading="loading" size="small">
          确 定
        </el-button>

      </template>
    </el-dialog>
</template>
<script>
import { union, xor, difference } from 'lodash'
import dayjs from 'dayjs'
export default {
  emits: ['confirmData'],
  props: {
    type: {
      default: 'image',
      type: String
    }
  },
  data () {
    return {
      // 日期时间插件
      dayjs,
      // loading
      loading: false,
      // 选择框显示
      resDialog: false,
      // 显示标题
      title: '',
      // 面包屑
      breadcrumb: [{ name: '根目录', path: '/' }],
      // 数据列表
      dataList: [],
      // 选择数据id
      checkList: [],
      // 分页数据
      pageInfo: { total: 0 },
      // 搜索参数
      queryParams: {
        origin_name: undefined,
        storage_path: '/',
        mime_type: 'image',
        page: 1,
        pageSize: 30
      }
    }
  },
  methods: {

    // 显示modal
    show () {
      this.resDialog = true
      this.queryParams.mime_type = this.type === 'image' ? 'image' : ''
      this.title = this.type === 'image' ? '选择图片' : '选择文件'
      this.checkList = []
      this.getList()
    },

    // 关闭modal
    handleResClose () {
      this.resDialog = false
    },

    // 获取目录及文件
    getList () {
      this.$API.upload.getAllFiles(this.queryParams).then(res => {
        this.dataList = res.data.items
        this.pageInfo = res.data.pageInfo
      })
    },

    // 打开目录
    openFolder (folder, type) {
      if (type === 'in') {
        const parent = this.breadcrumb[this.breadcrumb.length - 1]
        this.breadcrumb.push({ name: folder, path: parent.path + '/' + folder })
      }
      if (type === 'out') {
        if (folder === '/') {
          this.breadcrumb = [{ name: '根目录', path: '/' }]
        } else {
          this.breadcrumb.pop()
        }
      }
      this.queryParams.storage_path = folder
      this.getList()
    },

    selectAdd (item) {
      this.checkList = xor(this.checkList, [item])
    },

    // 全选当前页
    selectAll () {
      this.checkList = union(this.checkList, this.dataList)
    },

    // 反选当前页
    selectInvert () {
      this.checkList = xor(this.checkList, this.dataList)
    },

    // 取消当前页选择
    selectCancel () {
      this.checkList = difference(this.checkList, this.dataList)
    },

    // 提交数据
    selectSubmit () {
      this.$emit('confirmData', this.checkList)
      this.resDialog = false
    }
  }
}
</script>

<style scoped>
:deep(.el-dialog__body) {
  padding: 10px 20px !important;
}
.breadcrumb {
  border: 1px solid #ebeef5;
  padding: 10px;
  border-radius: 2px;
  margin-top: 10px;
}
.file-list {
  display: inline-flex;
  flex-wrap: wrap;
  flex-direction: row;
}
.file-list .list {
  width: 105px;
  height: 120px;
  border: 1px solid #ebeef5;
  margin-right: 14px;
  margin-bottom: 14px;
  display: flex;
  flex-direction: column;
  border-radius: 2px;
  cursor: pointer;
}
.file-list .list:hover {
  background: rgb(188 188 188 / 29%);
}
.file-list .list:nth-child(6),
.file-list .list:nth-child(12),
.file-list .list:nth-child(18),
.file-list .list:nth-child(24),
.file-list .list:nth-child(30),
.file-list .list:nth-child(36),
.file-list .list:nth-child(42),
.file-list .list:nth-child(48),
.file-list .list:nth-child(54),
.file-list .list:nth-child(60) {
  margin-right: 0;
}
.list .icon {
  height: 92px;
  margin-right: 1px;
  color: rgb(255, 204, 102);
  font-size: 56px;
  text-align: center;
  padding: 12px 0;
}
.list .file {
  height: 94px;
  overflow: hidden;
  position: relative;
}
.list .file .image {
  width: 100%;
  height: 94px;
}
.list .name {
  text-align: center;
  line-height: 27px;
  height: 27px;
  width: 90%;
  font-size: 12px;
  margin: 0 auto;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.list .file .check {
  position: absolute;
  top: -1px;
  left: 2px;
  width: 100px;
  height: 20px;
  z-index: 9;
  overflow: hidden;
  color: #fff;
  text-shadow: 1px 1px 3px #333;
}
:deep(.el-checkbox__label) {
  padding-right: 2px;
  line-height: 16px;
  font-size: 12px;
  float: right;
}
</style>
