export default {
  data() {
    return {
      drawer: false,
      title: '',
      // 默认激活
      activeName: '',
      // 表单字段
      form: {
        id: '',
        table_name: '',
        module_name: '',
        table_comment: '',
        menu_name: '',
        package_name: '',
        remark: '',
        type: '',
        belong_menu_id: '0',
        namespace: '',
        generate_type: '0',
        generate_menus: [],
        build_menu: '0',
        options: {},
        columns: [],
      },

      settingForm: {
        userinfo: 'id',
        date: 'default',
        select: [ { name: '', value: ''} ],
        radio: [ { name: '', value: ''} ],
        checkbox: [ { name: '', value: ''} ],
        tabs: [ { name: '', value: ''} ],
        area: { type: 'select', value: 'code' },
      },

      menuList: [
        { name: '新增', value: 'save', comment: '勾选生成新增数据按钮菜单及接口', check: '1' },
        { name: '更新', value: 'update', comment: '勾选生成更新数据按钮菜单及接口', check: '1' },
        { name: '读取', value: 'read', comment: '勾选生成读取数据按钮菜单及接口', check: '1' },
        { name: '删除', value: 'delete', comment: '勾选生成真实删除按钮菜单及接口', check: '1' },
        { name: '回收站列表', value: 'recycle', comment: '勾选生成移到回收站列表、真实删除、恢复菜单及接口，确定该表有deleted_at字段，且模型引入了软删除。', check: '1' },
        { name: '修改状态', value: 'changeStatus', comment: '勾选生成修改状态按钮菜单及接口，该接口用于单个字段状态修改', check: '1' },
        { name: '自增自减', value: 'numberOperation', comment: '勾选生成数据自增自减按钮菜单及接口，该接口用于单个字段增减操作', check: '1' },
        { name: '导入', value: 'import', comment: '勾选生成导入按钮菜单、接口和DTO文件', check: '1' },
        { name: '导出', value: 'export', comment: '勾选生成导出按钮菜单、接口和DTO文件', check: '1' },
      ],

      // 保存loading
      saveLoading: false,

      // 验证规则
      rules: {
        table_comment: [{ required: true, message: '请填写表描述', trigger: 'blur' }],
        module_name: [{ required: true, message: '请选择所属模块（注意对应表模块前缀）', trigger: 'change' }],
        // belong_menu_id: [{ required: true, message: '请选择所属菜单', trigger: 'change' }],
        menu_name: [{ required: true, message: '请选择所属菜单', trigger: 'blur' }],
        // package_name: [{ required: false, pattern: /^[A-Za-z]{3,}$/g, message: '包名必须为3位字母及以上', trigger: 'blur' }]
      },

      tree_id: '',
      tree_parent_id: '',
      tree_name: '',

      // 关联关系
      relations: [],
      realtionsType: [
        { name: '一对一', value: 'hasOne' },
        { name: '一对多', value: 'hasMany' },
        { name: '一对多（反向)', value: 'belongsTo' },
        { name: '多对多', value: 'belongsToMany' },
      ],

      // 当前记录
      record: null,

      // 字段列表
      columns: [],
      // 菜单列表
      menus: [],
      // 角色列表
      roles: [],
      // 字典列表
      dict: [],
      // 模块信息
      sysinfo: {},

      selectField: '',

      // 查询类型
      queryType: [
        { label: '=', value: 'eq' },
        { label: '!=', value: 'neq' },
        { label: '>', value: 'gt' },
        { label: '>=', value: 'gte' },
        { label: '<', value: 'lt' },
        { label: '<=', value: 'lte' },
        { label: 'LIKE', value: 'like' },
        { label: 'BETWEEN', value: 'between' },
      ],
      // 页面控件
      viewComponent: [
        { label: '文本框', value: 'text' },
        { label: '密码框', value: 'password' },
        { label: '文本域', value: 'textarea' },
        { label: '计数器', value: 'inputNumber' },
        { label: 'Switch开关', value: 'switch' },
        { label: '滑块', value: 'slider' },
        { label: '下拉框', value: 'select' },
        { label: '单选框', value: 'radio' },
        { label: '复选框', value: 'checkbox' },
        { label: '日期选择器', value: 'date' },
        { label: '时间选择器', value: 'time' },
        { label: '评分器', value: 'rate' },
        { label: '颜色选择器', value: 'colorPicker' },
        // { label: '分片上传', value: 'chunkUpload' },
        { label: '用户选择器', value: 'userSelect' },
        { label: '用户信息', value: 'userinfo' },
        { label: '省市区联动', value: 'area' },
        { label: '资源选择单选', value: 'selectResourceRadio' },
        { label: '资源选择多选', value: 'selectResourceMulti' },
        { label: '图片上传', value: 'image' },
        { label: '文件上传', value: 'file' },
        { label: '富文本控件', value: 'editor' },
        { label: '标签页', value: 'tabs' },
      ]
    }
  },
}