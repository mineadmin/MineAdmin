<template>
	<el-dialog :title="titleMap[mode]" v-model="visible" :width="550" destroy-on-close @closed="$emit('closed')">
		<el-form :model="form" :rules="rules" ref="dialogForm" label-width="105px">

			<el-form-item label="任务名称" prop="name">
				<el-input v-model="form.name" placeholder="请输入任务名称" clearable />
			</el-form-item>

			<el-form-item label="任务类型" prop="type">
				<el-select v-model="form.type" placeholder="请选择任务类型" style="width: 100%">
					<el-option
						v-for="item in types"
						:key="item.value"
						:label="item.label"
						:value="item.value"
					/>
				</el-select>
			</el-form-item>

			<el-form-item label="Cron规则" prop="rule">
				<template #label>
					Cron规则
					<el-tooltip>
						<template #content>
							0ㅤ1ㅤ2ㅤ3ㅤ4ㅤ5<br />
							*ㅤ*ㅤ*ㅤ*ㅤ*ㅤ* (执行)<br />
							-ㅤ-ㅤ-ㅤ-ㅤ-ㅤ- (忽略)<br />
							|ㅤ |ㅤ |ㅤ |ㅤ |ㅤ |<br />
							|ㅤ |ㅤ |ㅤ |ㅤ |ㅤ +----- 天或者星期 (0 - 6) (星期天=0)<br />
							|ㅤ |ㅤ |ㅤ |ㅤ +----- 月 (1 - 12)<br />
							|ㅤ |ㅤ |ㅤ +------- 天 (1 - 31)<br />
							|ㅤ |ㅤ +--------- 小时 (0 - 23)<br />
							|ㅤ +----------- 分钟 (0 - 59)<br />
							+------------- 秒 (0-59)<br />
						</template>
						<el-icon><el-icon-question-filled /></el-icon>
					</el-tooltip>
				</template>

				<sc-cron v-model="form.rule" placeholder="请输入Cron定时规则" clearable :shortcuts="shortcuts" />
			</el-form-item>

			<el-form-item label="调用目标" prop="target">
				<el-input type="textarea" :rows="3" placeholder="调用目标" v-model="form.target" clearable />
			</el-form-item>

			<el-form-item label="调用参数" prop="parameter">
				<el-input type="textarea" :rows="3" placeholder="调用参数，url和eval任务参数无效" v-model="form.parameter" clearable />
			</el-form-item>

			<el-form-item label="状态" prop="status">
				<el-radio-group v-model="form.status">

				<el-radio label="0">启用</el-radio>
				<el-radio label="1">停用</el-radio>

				</el-radio-group>
			</el-form-item>

			<el-form-item label="单次执行" prop="singleton">
				<el-radio-group v-model="form.singleton">

				<el-radio label="0">是</el-radio>
				<el-radio label="1">否</el-radio>

				</el-radio-group>
			</el-form-item>

			<el-form-item label="备注" prop="remark">
				<el-input type="textarea" :rows="3" placeholder="备注信息" v-model="form.remark" />
			</el-form-item>

		</el-form>
		<template #footer>
			<el-button @click="visible=false" >取 消</el-button>
			<el-button type="primary" :loading="isSaveing" @click="submit()">保 存</el-button>
		</template>
	</el-dialog>

</template>

<script>
	import scCron from '@/components/scCron';
	export default {
		emits: ['success', 'closed'],

		components: {
			scCron
		},

		data() {
			return {
				mode: "add",
				titleMap: {
					add: '新增定时任务',
					edit: '编辑定时任务'
				},
				form: {
					id:"",
					name: "",
					type: "",
					target: "",
					parameter: "",
					rule: "",
					singleton: "1",
					status: "0",
					remark: ""
				},
				rules: {
					name: [{ required: true, message: '请输入任务名称', trigger: 'blur' }],
					type: [{ required: true, message: '请选择任务类型', trigger: 'change' }],
					target: [{ required: true, message: '请输入任务调用目标', trigger: 'blur' }],
					rule: [{ required: true, message: '请输入任务执行规则', trigger: 'blur' }],
				},
				types: [
					{label: 'command（命令任务）', value: '1'},
					{label: 'class（类任务，执行类的execute方法）', value: '2'},
					{label: 'url（执行url地址任务）', value: '3'},
					{label: 'eval（PHP脚本任务，直接输入PHP脚本）', value: '4'},
				],
				visible: false,
				isSaveing: false,
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
				this.$refs.dialogForm.validate((valid) => {
					if (valid) {
						this.isSaveing = true;
						if (this.mode == 'add') {
							this.$API.crontab.save(this.form).then(res => {
								this.$message.success(res.message)
								this.isSaveing = false
								this.visible = false
								this.$emit('success')
							})
						} else {
							this.$API.crontab.update(this.form.id, this.form).then(res => {
								this.$message.success(res.message)
								this.isSaveing = false
								this.visible = false
								this.$emit('success')
							})
						}
					}
				})
			},

			//表单注入数据
			setData(data){
				this.form.id = data.id
				this.form.name = data.name
				this.form.type = data.type
				this.form.target = data.target
				this.form.parameter = data.parameter
				this.form.rule = data.rule
				this.form.singleton = data.singleton
				this.form.status = data.status
				this.form.remark = data.remark
			},

			openExpression() {
				this.$refs.expression.init()
			},

			updateExpression(val) {
				this.form.rule = val
			}
		}
	}
</script>

<style>
</style>
