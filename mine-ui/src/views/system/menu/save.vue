<template>
  <el-row>
    <el-col :xl="12" :lg="16">

      <h2>{{form.name || "新增菜单"}}</h2>
      <el-form :model="form" :rules="rules" ref="form" label-width="80px" label-position="right">

        <el-form-item label="菜单名称" prop="name">
          <el-input v-model="form.name" clearable placeholder="菜单显示名字"></el-input>
        </el-form-item>

        <el-form-item label="上级菜单" prop="parent_id" v-show="!form.top">
          <el-cascader v-model="form.parent_id" :options="menu" style="width:100%" :props="menuProps" :show-all-levels="false" clearable></el-cascader>
          <div class="el-form-item-msg">上级菜单，如果不选择则为顶级菜单。</div>
        </el-form-item>

        <el-form-item label="类型" prop="type">
          <el-radio-group v-model="form.type">

            <el-radio-button
              label="M"
            >菜单</el-radio-button>

            <el-radio-button
              label="B"
              v-if="!form.top"
            >按钮</el-radio-button>

            <el-radio-button
              label="L"
              v-if="!form.top"
            >外链</el-radio-button>

            <el-radio-button
              label="I"
              v-if="!form.top"
            >Iframe</el-radio-button>

          </el-radio-group>
        </el-form-item>

        <el-form-item label="代码标识" prop="code">
          <el-input v-model="form.code" clearable placeholder="菜单代码"></el-input>
          <div class="el-form-item-msg">系统唯一且与内置组件名一致，否则导致缓存失效。如类型为Iframe的菜单，别名将代替源地址显示在地址栏</div>
        </el-form-item>

        <el-form-item label="菜单图标" prop="icon" v-if="form.type != 'B'">
          <sc-icon-select v-model="form.icon" clearable style="width:100%"></sc-icon-select>
        </el-form-item>

        <el-form-item label="路由地址" prop="route" v-if="form.type != 'B'">
          <el-input v-model="form.route" clearable placeholder="请输入路由地址"></el-input>
        </el-form-item>

        <el-form-item label="重定向" prop="redirect" v-if="form.type != 'B' && form.type == 'M'">
          <el-input v-model="form.redirect" clearable placeholder="重定向地址"></el-input>
          <div class="el-form-item-msg">如果有重定向，那么路由地址将不会生效</div>
        </el-form-item>

        <el-form-item label="组件" prop="component" v-if="form.type != 'B' && form.type == 'M'">
          <el-autocomplete v-model="form.component" :fetch-suggestions="querySearch" :debounce="10" clearable style="width:100%" placeholder="请选择组件"></el-autocomplete>
          <div class="el-form-item-msg">如父节点、链接或Iframe等没有视图的菜单不需要填写</div>
        </el-form-item>

        <el-form-item label="排序" prop="sort" v-if="form.type != 'B'">
          <el-input-number v-model="form.sort" controls-position="right" :min="0" :max="999"></el-input-number>
          <div class="el-form-item-msg">菜单排序，数字大的在前面</div>
        </el-form-item>

        <el-form-item label="是否隐藏" prop="is_hidden" v-if="form.type != 'B'">
          <el-radio-group v-model="form.is_hidden">
            <el-radio label="0">是</el-radio>
            <el-radio label="1">否</el-radio>
          </el-radio-group>
          <div class="el-form-item-msg" true-label="0" false-label="1" label="0">菜单不显示在导航中，但用户依然可以访问，例如详情页</div>
        </el-form-item>

        <el-form-item label="是否停用" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio label="0">启用</el-radio>
            <el-radio label="1">停用</el-radio>
          </el-radio-group>
          <div class="el-form-item-msg" true-label="0" false-label="1" label="0">停用的菜单不会在导航中，也无法访问</div>
        </el-form-item>

        <el-form-item label="生成按钮" prop="restful" v-if="! form.id && form.type == 'M' ">
          <el-radio-group v-model="form.restful">
            <el-radio label="0">生成</el-radio>
            <el-radio label="1">不生成</el-radio>
          </el-radio-group>
          <div class="el-form-item-msg" true-label="0" false-label="1" label="1">生成RESTful路由按钮菜单，即：save、update、delete、read、import、export 以及回收站相关按钮菜单</div>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="submitForm" :loading="loading">保 存</el-button>
        </el-form-item>
      </el-form>

    </el-col>
  </el-row>

</template>

<script>
  import scIconSelect from '@/components/scIconSelect'

  export default {
    components: {
      scIconSelect
    },
    props: {
      menu: { type: Object, default: () => {} },
    },
    data(){
      return {
        form: {
          id: undefined,
          parent_id: undefined,
          top: undefined,
          isBtn: undefined,
          levels: [],
          name: '',
          route: '',
          component: '',
          redirect: '',
          code: '',
          status: '0',
          is_hidden: '0',
          icon: '',
          sort: 0,
          type: 'M',
          restful: '1'
        },
        menuProps: {
          value: 'id',
          label: 'name',
          checkStrictly: true
        },
        level: [],
        rules: {
          name:  [{ required: true, message: '请输入菜单名称', trigger: 'blur' }],
          code:  [{ required: true, message: '请输入标识代码', trigger: 'blur' }],
          route: [{ required: true, message: '请输入路由', trigger: 'blur' }]
        },
        views: [],
        loading: false
      }
    },
    mounted() {
      this.views = this.getViews();
    },
    methods: {
      //表单注入数据
      setData(data, pid){
        this.form = data
        this.form.parent_id = pid
        //可以和上面一样单个注入，也可以像下面一样直接合并进去
        //Object.assign(this.form, data)
      },

      submitForm () {
        this.loading = true
        this.$refs.form.validate(valid => {
          if (valid) {
            if (! this.form.parent_id) {
              this.form.parent_id = 0
            }
            this.form.children = undefined
            // 新增
            if (! this.form.id) {
              this.$API.menu.save(this.form).then(res => {
                this.$message.success(res.message)
                this.loading = false
                this.$emit('ok')
              })
            // 更新
            } else {
              this.$API.menu.update(this.form.id, this.form).then(res => {
                this.$message.success(res.message)
                this.loading = false
                this.$emit('ok')
              })
            }
          } else {
            this.loading = false
            return false
          }
        })
      },

      //获取所有视图组件
      getViews(){
        const filesUrl = []
        //不知道为什么 require.context 会引起Webpack会一并把结果都打包进来使得此文件过大
        let files = require.context('@/views', true, /\.vue$/)
        files.keys().forEach(file => {
          // 如需删除index? .replace(/\/index$/, "")
          filesUrl.push({
            value: file.replace(/^\.\/(.*)\.\w+$/, '$1')
          })
        })
        return filesUrl;
      },

      // 视图组件列表过滤
      querySearch(queryString, cb){
      	let results = this.getViews();
        cb(results.filter(item => item.value.indexOf(queryString) !== -1))
      }
    }
  }
</script>

<style scoped lang="scss">
  h2 {font-size: 17px;color: #3c4a54;padding:0 0 30px 0;}
  :deep(.el-form-item__content) {
    display: block;
  }
  .dark { 
    h2 { color: #fff; }
  }
</style>
