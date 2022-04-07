<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">

        <el-form-item label="内容类型" prop="content_type">
            <el-input v-model="form.content_type" clearable placeholder="请输入内容类型" />
        </el-form-item>

				<el-form-item label="消息标题" prop="title">
					<el-input v-model="form.title" clearable placeholder="请输入消息标题" />
				</el-form-item>

        <el-form-item label="消息内容" prop="content">
            <el-input v-model="form.content" clearable placeholder="请输入消息内容" />
        </el-form-item>


				<el-form-item label="发送人" prop="send_by">

				<sc-table-select v-model="send_by" :api="api" :table-width="700" :props="props">
					<template #header="{form, submit}">
						<el-form :inline="true" :model="form">
							<el-form-item label="用户ID" prop="id">
								<el-input v-model="form.id" clearable placeholder="请输入用户ID" />
							</el-form-item>
							<el-form-item label="用户名" prop="username">
								<el-input v-model="form.username" clearable placeholder="请输入用户名" />
							</el-form-item>
							<el-form-item>
								<el-button type="primary" @click="submit">查询</el-button>
							</el-form-item>
						</el-form>
					</template>
					<el-table-column prop="id" label="ID" width="150"></el-table-column>
					<el-table-column prop="username" label="用户名" width="100"></el-table-column>
					<el-table-column prop="nickname" label="姓名" width="150"></el-table-column>
					<el-table-column prop="created_at" label="注册时间"></el-table-column>
				</sc-table-select>

				</el-form-item>

				<el-form-item label="接收人" prop="receive_by">

				<sc-table-select v-model="receive_by" :api="api" :table-width="700" multiple :props="props">
					<template #header="{form, submit}">
						<el-form :inline="true" :model="form">
							<el-form-item label="用户ID" prop="id">
								<el-input v-model="form.id" clearable placeholder="请输入用户ID" />
							</el-form-item>
							<el-form-item label="用户名" prop="username">
								<el-input v-model="form.username" clearable placeholder="请输入用户名" />
							</el-form-item>
							<el-form-item>
								<el-button type="primary" @click="submit">查询</el-button>
							</el-form-item>
						</el-form>
					</template>
					<el-table-column prop="id" label="ID" width="150"></el-table-column>
					<el-table-column prop="username" label="用户名" width="100"></el-table-column>
					<el-table-column prop="nickname" label="姓名" width="150"></el-table-column>
					<el-table-column prop="created_at" label="注册时间"></el-table-column>
				</sc-table-select>

				</el-form-item>

    </el-form>
    <template #footer>
      <el-button @click="visible=false" >取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="submit()">发 送</el-button>
    </template>
  </el-dialog>
</template>

<script>
  import editor from '@/components/scEditor'

  export default {
		name: 'tableselect',
    emits: ['success', 'closed'],
    components: {
      editor
    },
    data() {
      return {
				api: this.$API.user.getPageList,
				props: {
					label: 'nickname',
					value: 'id'
				},
        mode: "add",
        titleMap: {
          add: '新增消息',
          edit: '编辑消息'
        },
        form: {

           id: 0,
           content_id: 0,
           content_type: '',
					 title: '',
           content: '',
           receive_by: '',
           send_by: '',
        },
				receive_by: [],
				send_by: [],
        rules: {

        },
        visible: false,
        isSaveing: false,

        message_send_status_data: [],
        message_read_status_data: [],
      }
    },
    methods: {
      //显示
      open(mode='add'){
        this.mode = mode;
        this.visible = true;
        return this;
      },
      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = null
            if (this.mode == 'add') {
							let receiveIds = this.receive_by.map(item => {
								return item.id
							});
							this.form.receive_by = receiveIds;
							this.form.send_by = this.send_by.id;
              res = await this.$API.systemQueueMessage.send(this.form)
            }
            this.isSaveing = false;
            if(res.success){
              this.$emit('success', this.form, this.mode)
              this.visible = false;
              this.$message.success(res.message)
            }else{
              this.$alert(res.message, "提示", {type: 'error'})
            }
          }
        })
      }
    }
  }
</script>
