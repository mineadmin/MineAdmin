<template>
  <el-drawer
    v-model="drawer"
    :with-header="false"
    size="40%"
  >
    <el-main
      v-loading="loading"
      element-loading-background="rgba(255, 255, 255, 0.5)"
      element-loading-text="数据加载中..."
      style="height:100%;"
    >
      <el-tabs v-model="activeName">
        <el-tab-pane label="选择接口绑定" name="group">
          <el-button-group>
            <el-button size="small" icon="el-icon-check" @click="selectAll">全选</el-button>

            <el-button size="small" icon="el-icon-minus" @click="clear">清除</el-button>

          </el-button-group>

          <el-button-group>
            <el-button type="primary" @click="save">保存绑定</el-button>

            <el-button @click="close">关闭</el-button>

          </el-button-group>
          <div v-for="(group, index) in apiGroupData" :key="index">
            <h3 style="margin-top: 10px;">
              {{ group.name }}
              <el-checkbox
                v-model="checkAll[index]"
                @change="handleCheckAllChange(index, group.apis)"
                style="margin-left: 10px;">全选</el-checkbox>
            </h3> 
            <el-checkbox-group
              v-model="apiGroupCheckList[index]"
              class="ma-tree-border padding"
            >
              <el-checkbox
                v-for="(item, key) in group.apis"
                :key="key" :label="item.id"
                @change="handleCheckedChange(index, item, group.apis.length)"
              >
                {{item.name}}
              </el-checkbox>
            </el-checkbox-group>
          </div>
        </el-tab-pane>
        <!-- <el-tab-pane label="选择接口绑定" name="api">

        </el-tab-pane> -->
      </el-tabs>
    </el-main>
  </el-drawer>
</template>

<script>
import { union, xor } from 'lodash'
export default {
  emits: ['success'],
  data() {
    return {
      appId: '',
      drawer: false,
      loading: true,
      activeName: 'group',
      apiGroupData: [],
      apiGroupCheckList: [],
      checkAll:[],

      checkList: [],
      queryParams: {
        getApiList: true
      }
    };
  },
  methods: {
    async open(id) {
      this.appId = id
      this.drawer = true
      this.loading = true
      this.clear()
      await this.getApiGroupData()
      await this.setData()
    },

    setData() {
      this.$API.app.getBindApiList({ id: this.appId }).then(res => {
        // 设置数据
        if (res.success && res.data.length > 0) {
          res.data.map(id => {
            this.apiGroupData.map((rows, key) => {
              rows.apis.map(async item => {
                if (item.id == id) {
                  this.apiGroupCheckList[key].push(item.id)
                  await this.handleCheckedChange(key, item, rows.apis.length)
                }
              })
            })
          })
        }
      })
    },

    // 请求数据
    getApiGroupData() {
      this.$API.apiGroup.getSelectList(this.queryParams).then(res => {
        this.apiGroupData = res.data
        this.apiGroupData.forEach( _ => {
          this.apiGroupCheckList.push([])
          this.checkAll.push(false)
        })
        this.loading = false
      })
    },

    // 分组全选
    handleCheckAllChange(index, rows) {
      if (! this.checkAll[index]) {
        let list = Object.assign({}, this.checkList)
        this.apiGroupCheckList[index] = []
        rows.map(row => {
          this.checkList = xor(this.checkList, [row.id])
        })
        this.checkList = union(this.checkList, list)
      } else {
        rows.map(row => {
          this.apiGroupCheckList[index].push(row.id)
          this.checkList = union(this.checkList, [row.id])
        })
      }
    },

    handleCheckedChange(index, item, len) {
      this.checkList = xor(this.checkList, [item.id])
      this.apiGroupCheckList[index] = xor(this.apiGroupCheckList[index], item.id)
      this.checkAll[index] = this.apiGroupCheckList[index].length === len
    },

    // 全选当前页
    selectAll () {
      this.apiGroupData.map((row, key) => {
        this.checkAll[key] = true
        this.handleCheckAllChange(key, row.apis)
        row.apis.map(item => {
          this.checkList = union(this.checkList, [item.id])
        })
      })
    },

    // 清除
    clear () {
      this.apiGroupData.map((row, key) => {
        this.apiGroupCheckList[key] = []
        this.checkAll[key] = false
        this.checkList = []
      })
    },

    // 保存
    save() {
      this.$API.app.bind(this.appId, { apiIds: this.checkList }).then(res => {
        if (res.success) {
          this.$message.success(res.message)
          this.close()
        }
      })
    },

    // 关闭
    close() {
      this.apiGroupData = []
      this.apiGroupCheckList = []
      this.checkAll = []
      this.checkList = []
      this.drawer = false
    }
  }
};
</script>

<style scoped>
.padding {
  padding: 5px 10px;
}
</style>
