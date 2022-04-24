export default {
  methods: {
    addRelation() {
      let relation = {
        name: '', type: 'hasOne', model: '', foreignKey: '', localKey: '', table: ''
      }
      this.relations.push(relation)
    },

    delRelation(index) {
      this.relations.splice(index, 1)
    },

    handleChangeGenType(value) {
      if (value === '1') {
        this.$confirm('生成到模块会覆盖原文件，确定使用该方式吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then().catch(_=> {
          this.form.generate_type = '0'
        })
      }
    },

    handleBuildMenu(value) {
      if (value === '1') {
        this.$confirm('确定选择生成代码时执行菜单SQL语句', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then().catch(_=> {
          this.form.build_menu = '0'
        })
      }
    },

    handleClose () {
      this.drawer = false
      this.selectField = ''
      this.componentInfo = {}
    },

    getMenu () {
      this.$API.menu.tree({ onlyMenu: true }).then(res => {
        this.menus = res.data
      })
    },

    getRoles () {
      this.$API.role.getList().then(res => {
        this.roles = res.data
      })
    },

    getSystemInfo () {
      this.$API.table.getSystemInfo().then(res => {
        this.sysinfo = res.data
      })
    },

    // 请求字典列表
    getDictType () {
      this.$API.dictType.getTypeList().then(res => {
        this.dict = res.data
      })
    },

    // 请求表字段
    getTableColumns () {
      this.$API.generate.getTableColumns({ table_id: this.record.id }).then(res => {
        this.columns = res.data
      })
    },

    // 请求所有模型
    getModels () {
      this.$API.generate.getModels().then( res => {
        this.models = res.data
      })
    },

    // 请求所有数据表
    getTables () {
      this.$API.dataMaintain.getPageList({ pageSize: 999 }).then( res => {
        this.tables = res.data.items
      })
    },

    settingComponent(row, index) {
      let showDrawerList = [
        'date', 'userinfo', 'select', 'radio', 'checkbox', 'area', 'tabs'
      ]
      row.$index = index
      if (showDrawerList.includes(row.view_type)) {
        this.selectField = row
        if (row.options && row.options[row.view_type]) {
          this.settingForm[row.view_type] = row.options[row.view_type]
        }
        this.drawer = true
      }
    },

    handleSetting() {
      let index = this.selectField.$index;
      if (! this.columns[index].options) {
        this.columns[index].options = {}
      }
      let view_type = ['userinfo', 'date', 'select', 'redio', 'checkbox', 'area', 'tabs']
      
      if (view_type.includes(this.selectField.view_type)) {
        this.columns[index].options[this.selectField.view_type] = this.settingForm[this.selectField.view_type]
      }

      this.handleClose()
    },

    // 提交数据
    handleSubmit () {
      this.$refs.form.validate(async (valid) => {
        if (valid) {
          this.form.columns = this.columns
          this.saveLoading = true
          this.form.generate_menus = []
          this.menuList.map(item => {
            if (item.check === '1') this.form.generate_menus.push(item.value)
          })
          
          this.form.options = { relations: this.relations, tree_id: this.tree_id, tree_parent_id: this.tree_parent_id, tree_name: this.tree_name }
          let res = await this.$API.generate.update(this.form)
          this.saveLoading = false
          if (res.success) {
            this.record = null
            this.$message.success(res.message)
          } else {
            this.$alert(res.message, "提示", { type: 'error' })
          }
        }
      })
    },

    // 为form赋值
    setFormValue () {
      this.form.id = this.record.id
      this.form.table_name = this.record.table_name
      this.form.table_comment = this.record.table_comment
      this.form.module_name = this.record.module_name
      this.form.menu_name = this.record.menu_name
      this.form.belong_menu_id = this.record.belong_menu_id
      this.form.package_name = this.record.package_name
      this.form.remark = this.record.remark
      this.form.type = this.record.type
      this.form.generate_type = this.record.generate_type
      this.form.build_menu = this.record.build_menu
      this.form.component_type = this.record.component_type

      const menuList = this.record.generate_menus
      if (menuList) {
        this.menuList.map( (item, index) => {
          this.menuList[index].check = menuList.includes(item.value) ? '1' : '0'
        })
      }

      if (this.record.options && this.record.options.relations) {
        this.record.options.relations.map(item => {
          this.relations.push(item)
        })
      }

      if (this.form.type == 'tree') {
        this.tree_id = this.record.options.tree_id
        this.tree_parent_id = this.record.options.tree_parent_id
        this.tree_name = this.record.options.tree_name
      }
    },

    // 选择模块处理
    hanldeChangeModule (val) {
      this.form.module_name = val
    },
  }  
}