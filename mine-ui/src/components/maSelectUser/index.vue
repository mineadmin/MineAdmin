<template>
  <el-button icon="el-icon-finished" @click="open" >选择用户</el-button> <el-tag>已选择 {{modelValue.length}} 个用户</el-tag>

  <el-dialog
    append-to-body
    v-model="showSelectPanel"
    title="选择用户"
    width="1020px"
  >
    <el-form :inline="true" :model="queryParams">
      <el-form-item label="">
        <el-input v-model="queryParams.username" placeholder="通过账号查找" clearable />
      </el-form-item>
      <el-form-item label="">
        <el-input v-model="queryParams.nickname" placeholder="通过昵称查找" clearable />
      </el-form-item>
      <el-form-item label="">
        <el-input v-model="queryParams.phone" placeholder="通过联系方式查找" clearable />
      </el-form-item>
      <el-form-item label="">
        <el-input v-model="queryParams.email" placeholder="通过邮箱查找" clearable />
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="search">搜索</el-button>
        <el-button @click="reset">重置</el-button>
      </el-form-item>
    </el-form>
    <el-tabs v-model="activeName" @tab-click="handleTabsClick">
      <el-tab-pane label="按条件" name="condition">
        <maTable
          ref="condition"
          :api="api"
          :column="columns"
          @selection-change="selectionChange"
          :params="defaultParams"
          stripe
          remoteSort
          remoteFilter
        >
          <el-table-column type="selection" width="50"></el-table-column>
        </maTable>
      </el-tab-pane>

      <el-tab-pane label="按部门" name="dept">
        <el-row>
          <el-col :span="6">
            <el-tree
              ref="deptTree"
              node-key="id"
              :data="deptData"
              :current-node-key="0"
              :highlight-current="true"
              :expand-on-click-node="false"
              @node-click="deptClick"
            ></el-tree>
          </el-col>

          <el-col :span="18">
            <maTable
              ref="dept"
              :api="api"
              :column="columns"
              @selection-change="selectionChange"
              :params="defaultParams"
              stripe
              remoteSort
              remoteFilter
            >
              <el-table-column type="selection" width="50"></el-table-column>
            </maTable>
          </el-col>
        </el-row>
      </el-tab-pane>

      <el-tab-pane label="按角色" name="role">
        <el-row>
          <el-col :span="4">
            <el-tree
              ref="roleList"
              node-key="id"
              :data="roleData"
              :current-node-key="0"
              :highlight-current="true"
              :expand-on-click-node="false"
              @node-click="roleClick"
            ></el-tree>
          </el-col>

          <el-col :span="20">
            <maTable
              ref="role"
              :api="api"
              :column="columns"
              @selection-change="selectionChange"
              :params="defaultParams"
              stripe
              remoteSort
              remoteFilter
            >
              <el-table-column type="selection" width="50"></el-table-column>
            </maTable>
          </el-col>
        </el-row>
      </el-tab-pane>

      <el-tab-pane label="按岗位" name="post">
        <el-row>
          <el-col :span="4">
            <el-tree
              ref="postList"
              node-key="id"
              :data="postData"
              :current-node-key="0"
              :highlight-current="true"
              :expand-on-click-node="false"
              @node-click="postClick"
            ></el-tree>
          </el-col>

          <el-col :span="20">
            <maTable
              ref="post"
              :api="api"
              :column="columns"
              @selection-change="selectionChange"
              :params="defaultParams"
              stripe
              remoteSort
              remoteFilter
            >
              <el-table-column type="selection" width="50"></el-table-column>
            </maTable>
          </el-col>
        </el-row>
      </el-tab-pane>
    </el-tabs>

    <template #footer>
      <el-button type="primary" @click="confirmSelect">确定选择</el-button>
      <el-button @click="cancel">关闭</el-button>
    </template>
  </el-dialog>

</template>

<script>
export default {
  props: {
    'modelValue': {
      type: Array
    }
  },
  data() {
    return {
      showSelectPanel: false,
      activeName: 'condition',
      api: { list: this.$API.common.getUserList },
      selection: [],
      columns: [
        {
          label: '登录账号',
          prop: 'username',
          sortable: 'custom'
        },
        {
          label: '昵称',
          prop: 'nickname',
        },
        {
          label: '联系电话',
          prop: 'phone',
        },
        {
          label: '邮箱',
          prop: 'email',
        }
      ],
      defaultParams: {
        select: 'id, username, nickname, phone, email',
        pageSize: 10,
      },
      queryParams: {},

      deptData: null,
      roleData: null,
      postData: null,
    }
  },

  methods: {

    open() {
      this.$nextTick(() => {
        if (this.$refs.condition) {
          this.$refs.condition.clearSelection()
        }
        if (this.$refs.dept) {
          this.$refs.dept.clearSelection()
        }
        if (this.$refs.role) {
          this.$refs.role.clearSelection()
        }
        if (this.$refs.post) {
          this.$refs.post.clearSelection()
        }
      })
      this.showSelectPanel = true
    },

    confirmSelect() {
      if (this.selection.length === 0) {
        this.$confirm('没有选择任何用户，确定吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.showSelectPanel = false
          this.$emit('update:modelValue', this.selection)
        })
      } else {
        this.$emit('update:modelValue', this.selection)
        this.showSelectPanel = false
        this.$message.success('选择成功')
      }
    },

    cancel() {
      this.showSelectPanel = false
    },

    handleTabsClick(tab, ev) {
      this.selection = []
      if (tab.props.name === 'dept' && ! this.deptData) {
        this.$API.common.getDeptTreeList().then(res => {
          res.data.unshift({ id: 0, label: '所有部门' })
          this.deptData = res.data
        })
      }

      if (tab.props.name === 'role' && ! this.roleData) {
        this.$API.common.getRoleList().then(res => {
          this.roleData = res.data.map(item => {
            return { id: item.id, label: item.name, value: item.id }
          })
        })
      }

      if (tab.props.name === 'post' && ! this.postData) {
        this.$API.common.getPostList().then(res => {
          this.postData = res.data.map(item => {
            return { id: item.id, label: item.name, value: item.id }
          })
        })
      }
    },

    roleClick(data) {
      if (this.queryParams.role_id == data.id) {
        return
      }
      this.queryParams.role_id = data.id
      this.queryParams.dept_id = undefined
      this.queryParams.post_id = undefined
      this.$refs.role.upData(this.queryParams)
    },

    postClick(data) {
      if (this.queryParams.post_id == data.id) {
        return
      }
      this.queryParams.post_id = data.id
      this.queryParams.dept_id = undefined
      this.queryParams.role_id = undefined
      this.$refs.post.upData(this.queryParams)
    },

    deptClick(data) {
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
      this.queryParams.role_id = undefined
      this.queryParams.post_id = undefined
      this.$refs.dept.upData(this.queryParams)
    },

    selectionChange(row) {
      this.selection = row.map(item => item.id)
    },

    search() {
      this.$refs[this.activeName].upData(this.queryParams)
    },

    reset() {
      this.queryParams = {
        username: undefined,
        nickname: undefined,
        phone: undefined,
        email: undefined
      }
      this.search()
    }
  }
}
</script>