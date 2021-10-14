<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          icon="el-icon-plus"
          v-auth="['setting:module:save']"
          type="primary"
          @click="add"
        >新增</el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">
          <el-input v-model="queryParams.label" placeholder="模块标签" clearable></el-input>

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

              <el-form-item label="模块名称" prop="name">
                <el-input v-model="queryParams.name" placeholder="模块名称" clearable></el-input>
              </el-form-item>

            </el-form>
          </el-popover>
        </div>
      </div>
    </el-header>
    <el-main class="nopadding">
      <maTable ref="table" :api="api" stripe>
        
        <el-table-column
          label="模块名称"
          prop="name"
          width="150"
        ></el-table-column>

        <el-table-column
          label="模块名称"
          prop="label"
          width="150"
        ></el-table-column>

        <el-table-column
          label="版本"
          prop="version"
          width="130"
        ></el-table-column>

        <el-table-column
          label="描述"
          prop="description"
        >
        </el-table-column>

        <el-table-column
          label="状态"
          prop="installed"
          width="100"
        >
          <template #default="scope">
            {{scope.row.installed ? '正常' : '已卸载'}}
          </template>
        </el-table-column>

        <el-table-column label="操作" fixed="right" align="right" width="130" >

          <template #default="scope">
            <el-button
              type="text" 
              :disabled="scope.row.name == 'System' || scope.row.name == 'Setting'" 
              v-auth="['setting:module:delete']" 
              @click="handleDelete(scope.row.name)"
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

  export default {
    name: 'setting:module',
    components: {
      saveDialog
    },

    data() {
      return {

        dialog: {
          save: false
        },

        povpoerShow: false,

        api: {
          list: this.$API.module.getPageList
        },

        queryParams: {
          name: undefined,
          label: undefined,
        }
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

      // 删除
      async handleDelete(id) {
        await this.$confirm(`确定删除该模块吗？`, '提示', {
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();

          this.$API.module.deletes(id).then(() => {
            this.$refs.table.upData(this.queryParams)
          })

          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      //搜索
      handlerSearch(){
        this.$refs.table.upData(this.queryParams)
      },

      resetSearch() {
        this.queryParams = {
          name: undefined,
          label: undefined
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

<style>
</style>
