<template>
  <el-container>
    <el-aside width="220px" v-loading="showDeptloading">
      <el-container>
        <el-header>
          <el-input placeholder="输入关键字进行过滤" v-model="deptFilterText" clearable></el-input>
        </el-header>
        <el-main class="nopadding">
          <el-tree
            ref="dept"
            class="menu"
            node-key="id"
            :data="dept"
            :current-node-key="0"
            :highlight-current="true"
            :expand-on-click-node="false"
            :filter-node-method="deptFilterNode"
            @node-click="deptClick"
          ></el-tree>
        </el-main>
      </el-container>
    </el-aside>
    <el-container>
        <el-header>
          <div class="left-panel">

            <el-button
              icon="el-icon-plus"
              v-auth="['system:user:save']"
              type="primary"
              @click="add"
            >新增</el-button>

            <el-button
              type="danger"
              plain
              icon="el-icon-delete"
              v-auth="['system:user:delete']"
              :disabled="selection.length==0"
              @click="batchDel"
            >删除</el-button>

            <el-button
              icon="el-icon-download"
              v-auth="['system:user:export']"
              @click="exportExcel"
            >导出</el-button>

            <ma-import
              :auth="['system:user:import']"
              :upload-api="$API.user.importExcel"
              :download-tpl-api="$API.user.downloadTemplate"
              @success="handleSuccess()"
            />

          </div>
          <div class="right-panel">
            <div class="right-panel-search">
              <el-input v-model="queryParams.username" placeholder="搜索用户名" clearable></el-input>

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
                  <el-form-item label="状态" prop="status">
                    <el-select  v-model="queryParams.status" clearable placeholder="用户状态">
                      <el-option label="启用" value="0">启用</el-option>
                      <el-option label="停用" value="1">停用</el-option>
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
        <el-main class="nopadding">
          <maTable
            ref="table"
            :api="api"
            :column="column"
            :showRecycle="true"
            @selection-change="selectionChange"
            @switch-data="switchData"
            stripe
            remoteSort
            remoteFilter
          >
            <el-table-column type="selection" width="50"></el-table-column>

            <el-table-column label="头像" width="80">
              <template #default="scope">
                <el-avatar :src="scope.row.avatar" :style="{width: '30px', height: '30px'}"  @error="() => true">
                  <i class="el-icon-s-custom" />
                </el-avatar>
              </template>
            </el-table-column>
            
            <el-table-column
              label="登录账号"
              prop="username"
              width="200"
              sortable='custom'
            ></el-table-column>

            <el-table-column
              label="昵称"
              width="160"
              prop="nickname"
            ></el-table-column>

            <el-table-column
              label="邮箱"
              width="200"
              prop="email"
            ></el-table-column>

            <el-table-column
              label="状态"
              width="160"
              prop="status"
            >
              <template #default="scope">
                <el-switch
                  v-model="scope.row.status"
                  @change="handleStatus($event, scope.row)"
                  active-value="0"
                  inactive-value="1"
                ></el-switch>
              </template>
            </el-table-column>

            <el-table-column
              label="用户类型"
              width="160"
              prop="user_type"
            >
              <template #default="scope">
                {{ scope.row.user_type === '100' ? '系统用户' : '其他类型' }}
              </template>
            </el-table-column>

            <el-table-column
              label="创建时间"
              prop="created_at"
              width="260"
              sortable='custom'
            ></el-table-column>

            <!-- 正常数据操作按钮 -->
            <el-table-column label="操作" fixed="right" align="right" width="150" v-if="!isRecycle">
              <template #default="scope">

                <el-button
                  type="text"
                  
                  @click="show(scope.row, scope.$index)"
                >查看</el-button>

                <el-button
                  v-if="scope.row.username === 'superAdmin'"
                  type="text"
                  
                  @click="clearCache(scope.row)"
                  v-auth="['system:user:cache']"
                >更新缓存</el-button>

                <el-dropdown v-if="scope.row.username !== 'superAdmin'">

                  <el-button type="text">更多</el-button>

                  <template #dropdown>
                    <el-dropdown-menu>

                      <el-dropdown-item
                        @click="edit(scope.row, scope.$index)"
                        v-if="$AUTH('system:user:update')"
                      >编辑</el-dropdown-item>

                      <el-dropdown-item 
                        @click="setHomepage(scope.row)"
                        v-if="$AUTH('system:user:homePage')"
                      >设置首页</el-dropdown-item>

                      <el-dropdown-item 
                        @click="clearCache(scope.row)"
                        v-if="$AUTH('system:user:cache')"
                      >更新缓存</el-dropdown-item>

                      <el-dropdown-item 
                        @click="initUserPassword(scope.row.id)"
                        v-if="$AUTH('system:user:initUserPassword')"
                      >初始化密码</el-dropdown-item>

                      <el-dropdown-item
                        @click="deletes(scope.row.id)"
                        divided
                        v-if="$AUTH('system:user:delete')"
                      >删除</el-dropdown-item>

                    </el-dropdown-menu>
                  </template>

                </el-dropdown>
                
              </template>
            </el-table-column>

            <!-- 回收站操作按钮 -->
            <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
              <template #default="scope">

                <el-button
                  type="text"
                  v-auth="['system:user:recovery']"
                  @click="recovery(scope.row.id)"
                >恢复</el-button>

                <el-button
                  type="text"
                  v-auth="['system:user:realDelete']"
                  @click="deletes(scope.row.id)"
                >删除</el-button>

              </template>
            </el-table-column>

          </maTable>
        </el-main>
    </el-container>
  </el-container>

  <save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false"></save-dialog>
  <homepage-dialog v-if="dialog.homepage" ref="homepageDialog" @success="handleSuccess" @closed="dialog.homepage=false"></homepage-dialog>

</template>
<script>
  import saveDialog from './save'
  import homepageDialog from './setHomepage'

  export default {
    name: 'system:user',
    components: {
      saveDialog,
      homepageDialog
    },

    data() {
      return {
        dialog: {
          save: false,
          homepage: false
        },
        column: [
          { label: '用户ID', prop: 'id', width: '150', hide: true },
          { label: '手机', prop: 'phone', width: '120', hide: true  },
          { label: '最后登录时间', prop: 'login_time', width: '200', hide: true  },
          { label: '最后登录IP', prop: 'login_ip', width: '180', hide: true  }
        ],
        povpoerShow: false,
        dateRange:'',
        showDeptloading: false,
        deptFilterText: '',
        dept: [],
        api: {
          list: this.$API.user.getPageList,
          recycleList: this.$API.user.getRecyclePageList,
        },
        selection: [],
        queryParams: {
          username: undefined,
          dept_id: undefined,
          maxDate: undefined,
          minDate: undefined,
          status: undefined
        },
        isRecycle: false,
      }
    },
    watch: {
      deptFilterText(val) {
        this.$refs.dept.filter(val);
      }
    },
    mounted() {
      this.getDept()
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
      edit(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('edit').setData(row)
        })
      },
      //查看
      show(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('show').setData(row)
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
            this.$API.user.realDeletes(ids.join(',')).then(res => {
              res.success && this.$message.success(res.message)
              res.success || this.$message.error(res.message)
            })
          } else {
            this.$API.user.deletes(ids.join(',')).then(res => {
              res.success && this.$message.success(res.message)
              res.success || this.$message.error(res.message)
            })
          }
          this.$refs.table.upData(this.queryParams)
          loading.close();
        })
      },

      // 单个删除
      async deletes(id) {
        await this.$confirm(`确定删除该用户吗？`, '提示', {
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();
          if (this.isRecycle) {
            this.$API.user.realDeletes(id).then(res => {
              res.success && this.$message.success(res.message)
              res.success || this.$message.error(res.message)
            })
          } else {
            this.$API.user.deletes(id).then(res => {
              res.success && this.$message.success(res.message)
              res.success || this.$message.error(res.message)
            })
          }
          loading.close();
          this.$refs.table.upData(this.queryParams)
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.user.recoverys(id).then(res => {
          res.success && this.$message.success(res.message)
          res.success || this.$message.error(res.message)
          this.$refs.table.upData(this.queryParams)
        })
      },

      //表格选择后回调事件
      selectionChange(selection){
        this.selection = selection;
      },
      //加载树数据
      async getDept(){
        await this.$API.dept.tree().then(res => {
          res.data.unshift({id: 0, label: '所有部门'})
          this.dept = res.data
          this.showDeptloading = false
        })
      },
      //树过滤
      deptFilterNode(value, data){
        if (!value) return true;
        return data.label.indexOf(value) !== -1;
      },
      //树点击事件
      deptClick(data){
        if (data.id === 0) {
          data.id = undefined
        }
        if (this.queryParams.dept_id == data.id) {
          return
        }
        if (data.id === undefined) {
          this.queryParams.dept_id = data.id
        } else {
          let ids = [ data.id ]
          
          let filterNode = (nodes) => {
            nodes.map(item => {
              if (item.children && item.children.length > 0) {
                filterNode(item.children)
              } else {
                ids.push(item.id)
              }
            })
          }
          if (data.children && data.children.length > 0) {
            filterNode(data.children)
          }
          this.queryParams.dept_id = ids.join(',')
        }
        this.$refs.table.upData(this.queryParams)
      },

      // 初始化用户密码
      initUserPassword (id) {
        this.$confirm('确定要将用户密码设置为：123456', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$API.user.initUserPassword(id).then(() => {
            this.$message.success('用户密码初始化成功')
          })
        })
      },

      // 导出用户
      exportExcel () {
        this.$API.user.exportExcel(this.queryParams).then(res => {
          this.$TOOL.download(res)
        })
      },

      // 设置用户首页
      setHomepage(row) {
        this.dialog.homepage = true
        this.$nextTick(() => {
          this.$refs.homepageDialog.open().setData(row)
        })
      },

      // 更新用户缓存
      clearCache(row) {
        this.$API.user.clearCache({id: row.id}).then(res => {
          res.success || this.$message.error(res.message)
          res.success && this.$message.success('该用户缓存已清空')
        })
      },

      // 选择时间事件
      handleDateChange (values) {
        if (values !== null) {
          this.queryParams.minDate = values[0]
          this.queryParams.maxDate = values[1]
        }
      },

      //搜索
      handlerSearch(){
        this.$refs.table.upData(this.queryParams)
      },

      // 切换数据类型回调
      switchData(isRecycle) {
        this.isRecycle = isRecycle
      },

      // 用户状态更改
      handleStatus (val, row) {
        const status = row.status === '0' ? '0' : '1'
        const text = row.status === '0' ? '启用' : '停用'
        this.$confirm(`确认要${text} ${row.username} 用户吗？`, '提示', {
          type: 'warning',
          confirmButtonText: '确定',
          cancelButtonText: '取消'
        }).then(() => {
          this.$API.user.changeStatus({ id: row.id, status }).then(() => {
            this.$message.success(text + '成功')
          })
        }).catch(() => {
          row.status = row.status === '0' ? '1' : '0'
        })
      },

      resetSearch() {
        this.queryParams = {
          username: undefined,
          dept_id: undefined,
          maxDate: undefined,
          minDate: undefined,
          status: undefined
        }
        this.$refs.table.upData(this.queryParams)
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      }
    }
  }
</script>

<style scoped lang="scss">
:deep(.el-avatar--circle) {
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
