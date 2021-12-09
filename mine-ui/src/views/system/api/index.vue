<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          icon="el-icon-plus"
          v-auth="['system:api:save']"
          type="primary"
          @click="add"
        >新增</el-button>

        <el-button
          type="danger"
          plain
          icon="el-icon-delete"
          v-auth="['system:api:delete']"
          :disabled="selection.length==0"
          @click="batchDel"
        >删除</el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">

          <el-input v-model="queryParams.name" placeholder="接口名称" clearable></el-input>

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

            <el-form-item label="应用名称" prop="group_id">
              <el-select v-model="queryParams.group_id" style="width:100%" clearable placeholder="应用分组">
                <el-option v-for="(item, index) in groupData" :key="index" :value="item.id" :label="item.name" />
              </el-select>
            </el-form-item>

            <el-form-item label="认证模式" prop="auth_mode">
                <el-input v-model="queryParams.auth_mode" placeholder="认证模式" clearable></el-input>
            </el-form-item>

            <el-form-item label="请求模式" prop="request_mode">

            <el-select v-model="queryParams.request_mode" style="width:100%" clearable placeholder="请求模式">
                <el-option
                    v-for="(item, index) in request_mode_data"
                    :key="index"
                    :label="item.label"
                    :value="item.value"
                >{{item.label}}</el-option>
            </el-select>
            </el-form-item>

            <el-form-item label="状态" prop="status">

            <el-select v-model="queryParams.status" style="width:100%" clearable placeholder="状态">
                <el-option
                    v-for="(item, index) in data_status_data"
                    :key="index"
                    :label="item.label"
                    :value="item.value"
                >{{item.label}}</el-option>
            </el-select>
            </el-form-item>

            </el-form>
          </el-popover>
        </div>
      </div>
    </el-header>
    <el-main class="nopadding">
      <maTable
        ref="table"
        :api="api"
        :column="column"
        :showRecycle="true"
        row-key="id"
        :hidePagination="false"
        @selection-change="selectionChange"
        @switch-data="switchData"
        stripe
        remoteSort
      >
        <el-table-column type="selection" width="50"></el-table-column>

        <el-table-column
           label="接口名称"
           prop="name"
           width="150"
        />

				<el-table-column
					label="访问名称"
					prop="access_name"
				/>

        <el-table-column
           label="类名称"
           prop="class_name"
					 show-overflow-tooltip
        />
        <el-table-column
           label="方法名"
           prop="method_name"
           width="180"
        />
        <el-table-column
           label="认证模式"
           prop="auth_mode"
           width="120"
        >
          <template #default="scope">
            <el-tag v-if="scope.row.auth_mode === '0'" type="success">简易模式</el-tag>
            <el-tag v-else type="danger">复杂模式</el-tag>
          </template>
        </el-table-column>

        <el-table-column
           label="请求模式"
           prop="request_mode"
           width="120"
        >
          <template #default="scope">
            <ma-dict-tag v-if="scope.row.request_mode === 'A'" :options="request_mode_data" :value="scope.row.request_mode" />
            <ma-dict-tag v-if="scope.row.request_mode === 'P'"  :tagType="'warning'" :options="request_mode_data" :value="scope.row.request_mode" />
            <ma-dict-tag v-if="scope.row.request_mode === 'G'" :tagType="'danger'" :options="request_mode_data" :value="scope.row.request_mode" />
          </template>
        </el-table-column>

        <el-table-column
           label="状态"
           prop="status"
           width="50"
        >
          <template #default="scope">
            <status-indicator :type="scope.row.status === '0' ? 'primary' : 'danger'" />
          </template>
        </el-table-column>

        <!-- 正常数据操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="230" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              @click="tableEdit(scope.row, scope.$index)"
              v-auth="['system:api:update']"
            >编辑</el-button>

            <el-button
              type="text"
              size="small"
              @click="goto('request', scope.row)"
              v-auth="['system:apiColumn']"
            >请求数据</el-button>

            <el-button
              type="text"
              size="small"
              @click="goto('response', scope.row)"
              v-auth="['system:apiColumn']"
            >响应数据</el-button>

            <el-button
              type="text"
              size="small"
              @click="deletes(scope.row.id)"
              v-auth="['system:api:delete']"
            >删除</el-button>

          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              v-auth="['system:api:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              size="small"
              v-auth="['system:api:realDelete']"
              @click="deletes(scope.row.id)"
            >删除</el-button>

          </template>
        </el-table-column>

      </maTable>
    </el-main>
  </el-container>

  <save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false"></save-dialog>

</template>

<script>
  import saveDialog from './save'
  import statusIndicator from  '@/components/scMini/scStatusIndicator'

  export default {
    name: 'system:api',
    components: {
      saveDialog,
      statusIndicator
    },

    async created() {
        await this.getDictData()
        await this.getGroupData()
    },

    data() {
      return {
        dialog: {
          save: false
        },

        request_mode_data: [],
        data_status_data: [],
        column: [],
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.api.getList,
          recycleList: this.$API.api.getRecycleList,
        },
        selection: [],
        queryParams: {

          group_id: undefined,
          name: undefined,
          auth_mode: undefined,
          request_mode: undefined,
          status: undefined,
        },
        isRecycle: false,

        groupData: [],
      }
    },
    methods: {

      //添加
      add(){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open()
        })
      },

      //编辑
      tableEdit(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('edit').then(_ => _.setData(row))
        })
      },

      //查看
      tableShow(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('show').then(_ => _.setData(row))
        })
      },

      //批量删除
      async batchDel(){
        await this.$confirm(`确定删除选中的 ${this.selection.length} 项吗？`, '提示', {
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();
          let ids = []
          this.selection.map(item => ids.push(item.id))
          if (this.isRecycle) {
            this.$API.api.realDeletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          } else {
            this.$API.api.deletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        })
      },

      // 单个删除
      async deletes(id) {
        await this.$confirm(`确定删除该数据吗？`, '提示', {
          type: 'warning'
        }).then(async () => {
          const loading = this.$loading();
          if (this.isRecycle) {
            await this.$API.api.realDeletes(id)
            this.$refs.table.upData(this.queryParams)
          } else {
            await this.$API.api.deletes(id)
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.api.recoverys(id).then(res => {
          this.$message.success(res.message)
          this.$refs.table.upData(this.queryParams)
        })
      },

      //表格选择后回调事件
      selectionChange(selection){
        this.selection = selection;
      },

      // 选择时间事件
      handleDateChange (values) {
        if (values !== null) {
          this.queryParams.minDate = values[0]
          this.queryParams.maxDate = values[1]
        }
      },

      // 跳转
      goto(type, row) {
        let params = { apiId: row.id, title: row.name, type }
        this.$router.push({ path: '/apiColumn', query: params })
      },

      //搜索
      handlerSearch(){
        this.$refs.table.upData(this.queryParams)
      },

      // 切换数据类型回调
      switchData(isRecycle) {
        this.isRecycle = isRecycle
      },

      resetSearch() {
        this.queryParams = {

          group_id: undefined,
          name: undefined,
          auth_mode: undefined,
          request_mode: undefined,
          status: undefined,
        }
        this.$refs.table.upData(this.queryParams)
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      },

      // 获取字典数据
      getDictData() {

          this.getDict('request_mode').then(res => {
              this.request_mode_data = res.data
          })
          this.getDict('data_status').then(res => {
              this.data_status_data = res.data
          })
      },

      // 获取组列表
      getGroupData() {
        this.$API.apiGroup.getSelectList().then(res => {
          if (res.success) {
            this.groupData = res.data
          }
        })
      }
    }
  }
</script>
