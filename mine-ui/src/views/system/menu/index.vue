<template>
  <el-container>
    <el-aside width="355px">
      <el-main
        v-loading="loading" element-loading-background="rgba(255, 255, 255, 0.01)"
        element-loading-text="菜单加载中..." style="height:100%; padding: 0">
        <el-container>
          <el-header>
            <el-input placeholder="输入关键字进行过滤" v-model="menuFilterText" clearable></el-input>
            <el-button
              icon="el-icon-plus"
              type="primary"
              v-auth="'system:menu:save'"
              @click="add()"
              v-if="!showRecycle"
              style="margin-left: 10px;"
            >新增</el-button>
          </el-header>
          <el-main class="nopadding">
            <el-tree
              ref="menu"
              class="menu"
              node-key="name"
              :data="menuList"
              :props="menuProps"
              show-checkbox
              highlight-current
              :filter-node-method="menuFilterNode"
              @node-click="menuClick"
            >

              <template #default="{node, data}">
                <span class="custom-tree-node">
                  <span class="label">{{ node.label }}</span>
                  <!-- 回收站数据显示按钮 -->
                  <span class="do" v-if="showRecycle">

                    <el-tooltip class="item" effect="dark" content="恢复菜单" placement="top">
                      <el-button
                        class="mini-button"
                        icon="el-icon-refresh-left"
                        v-auth="'system:menu:recovery'"
                        @click.stop="handleRecovery(data)"
                      />
                    </el-tooltip>

                    <el-tooltip class="item" effect="dark" content="物理删除" placement="top">
                      <el-button
                        class="mini-button"
                        icon="el-icon-delete"
                        v-auth="'system:menu:realDelete'"
                        @click.stop="handleRealDelete(data)"
                      />
                    </el-tooltip>
                  </span>
                  <!-- 正常数据显示按钮 -->
                  <span class="do" v-else>

                    <el-tag v-if="data.type === 'M'">菜单</el-tag>
                    <el-tag v-if="data.type === 'B'" type="success">按钮</el-tag>
                    <el-tag v-if="data.type === 'L'" type="warning">外链</el-tag>
                    <el-tag v-if="data.type === 'I'" type="danger">Iframe</el-tag>

                    <el-tooltip class="item" effect="dark" content="新增子菜单" placement="top">
                      <el-button
                        class="mini-button"
                        icon="el-icon-plus"
                        v-auth="'system:menu:save'"
                        @click.stop="add(node, data)"
                      />
                    </el-tooltip>

                    <el-tooltip class="item" effect="dark" content="移动回收站" placement="top">
                      <el-button
                        class="mini-button"
                        icon="el-icon-delete"
                        v-auth="'system:menu:delete'"
                        @click.stop="handleDelete(data)"
                      />
                    </el-tooltip>

                  </span>
                </span>
              </template>

            </el-tree>
          </el-main>
          <el-footer style="height:60px;">

            <el-button
              type="danger"
              plain
              icon="el-icon-delete"
              @click="deleteBatch"
            >删除按钮</el-button>

            <el-button
              icon="el-icon-view"
              v-auth="'system:menu:recycle'"
              @click="switchData"
            >{{ getSwitchText }}</el-button>

            <el-button
              icon="el-icon-refresh"
              @click="getMenu"
            >刷新</el-button>

          </el-footer>
        </el-container>
      </el-main>
    </el-aside>
    <el-container>
      <el-main class="nopadding" style="padding:20px;">
        <save ref="save" :menu="menuList" @ok="handleOk"></save>
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
  let newMenuIndex = 1;
  import save from './save'

  export default {
    name: "system:menu",
    components: {
      save
    },
    data(){
      return {
        loading: false,
        menuList: [],
        menuProps: {
          label: (data)=>{
            return data.name
          }
        },
        menuFilterText: '',
        showRecycle: false,
        queryParams: {
          name: undefined,
        }
      }
    },
    computed: {
      getSwitchText() {
        return this.showRecycle ? '正常数据' : '回收站'
      }
    },
    watch: {
      menuFilterText(val){
        this.$refs.menu.filter(val);
      }
    },
    mounted() {
      this.getMenu();
    },
    methods: {
      //加载树数据
      async getMenu(){
        this.loading = true
        if (! this.showRecycle) {
          await this.$API.menu.getList(this.queryParams).then(res => {
            this.menuList = res.data;
            this.loading = false
          })
        } else {
          await this.$API.menu.getRecycle(this.queryParams).then(res => {
            this.menuList = res.data;
            this.loading = false
          })
        }
      },

      // 批量删除
      deleteBatch() {
        this.$confirm('此操作只会删除已选择的按钮菜单，确定删除吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          let ids = []
          this.$refs.menu.getCheckedNodes().filter(item => {
            if (item.type === 'B') {
              ids.push(item.id)
            }
          })

          if (ids.length > 0) {
            if (! this.showRecycle) {
              this.$API.menu.deletes(ids.join(',')).then(res => {
                this.$message.success(res.message)
                this.getMenu()
              })
            } else {
              this.$API.menu.realDeletes(ids.join(',')).then(res => {
                this.$message.success(res.message)
                this.getMenu()
              })
            }
          } else {
            this.$message.error('选择项里没有按钮菜单，跳过删除')
          }
        }).catch(() => {})
      },

      //树点击
      menuClick(data, node){
        let pid = node.level===1?undefined:node.parent.data.id;
        if (! pid ) {
          data.top = 1
        }
        if (data.type === 'B') {
          data.isBtn = true
        }
        this.$refs.save.setData(data, pid)
      },
      //树过滤
      menuFilterNode(value, data){
        if (!value) return true;
        let targetText = data.name;
        return targetText.indexOf(value) !== -1;
      },

      //增加
      add(node){
        let newMenuName = "未命名" + newMenuIndex++;
        let newMenuData = {
          name: newMenuName,
          path: "",
          component: "",
          code: '',
          status: '0',
          is_hidden: '1',
          icon: '',
          sort: 0,
          type: 'M',
          restful: '1'
        }
        if(node){
          if (! node.data.level) {
            this.$message.error('请先保存上级菜单后，再新增菜单')
            return
          } else {
            if (node.data.type === 'B') {
              this.$message.error('按钮不允许创建子菜单')
              return
            }
            let level = node.data.level.split(',')
            if (level.length > 3) {
              this.$message.error('菜单最大为 4 层')
              return
            } else if(level.length === 3) {
              newMenuData.type = 'B'
            }
          }
          this.$refs.menu.append(newMenuData, node)
          let lastNode = node.childNodes[node.childNodes.length-1]
          this.$refs.menu.setCurrentKey(lastNode.data.name)
          let pid = node.data.id
          this.$refs.save.setData(newMenuData, pid)
          this.$refs.menu.getNode(node).expanded = true
        }else{
          this.$refs.menu.append(newMenuData)
          let newNode = this.menuList[this.menuList.length-1]
          newMenuData.top = 1
          this.$refs.menu.setCurrentKey(newNode.name)
          this.$refs.save.setData(newMenuData)
        }

      },

      handleOk () {
        this.getMenu()
      },

      // 移到回收站
      handleDelete (data) {
        if (! data.id) {
          this.$refs.menu.remove(data)
          this.$message.success('虚拟菜单已删除')
          return;
        }
        if (data.children && data.children.length > 0) {
          this.$message.error('请先删除子菜单')
          return;
        }
        this.$confirm('此操作会将数据移到回收站！', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$API.menu.deletes(data.id).then(res => {
            this.$message.success(res.message)
            this.getMenu()
          })
        }).catch(() => {})
      },

      // 真实删除数据
      handleRealDelete (data) {
        if (data.children && data.children.length > 0) {
          this.$message.error('请先删除子菜单')
          return;
        }
        this.$confirm('此操作会将数据物理删除', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$API.menu.realDeletes(data.id).then(res => {
            this.$message.success(res.message)
            this.getMenu()
          })
        }).catch(() => {})
      },

      // 恢复数据
      handleRecovery (data) {
        this.$API.menu.recoverys(data.id).then(res => {
          this.$message.success(res.message)
          this.getMenu()
        })
      },

      switchData () {
        this.showRecycle = !this.showRecycle
        this.getMenu()
        this.$message.success('数据已切换到' + ( this.showRecycle ? '回收站数据' : '正常数据' ))
      }
    }
  }
</script>

<style scoped>
  .custom-tree-node {display: flex;flex: 1;align-items: center;justify-content: space-between;font-size: 14px;padding-right: 24px;height:100%;}
  .custom-tree-node .label {display: flex;align-items: center;;height: 100%;}
  .custom-tree-node .label .el-tag {margin-left: 5px;}
  .custom-tree-node .do {display: none;}
  .custom-tree-node .do i {margin-left:5px;color: #999;padding:5px;}
  .custom-tree-node .do i:hover {color: #333;}

  .custom-tree-node:hover .do {display: inline-block;}

  .mini-button {
    margin-left: 10px; margin-top: -1px; height: 23px;
    padding: 0 5px;
  }
</style>
