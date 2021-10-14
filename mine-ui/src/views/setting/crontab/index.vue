<template>
	<el-main>
		<el-row :gutter="15">
			<el-col :xl="6" :lg="6" :md="8" :sm="12" :xs="24" v-for="item in list" :key="item.id">
				<el-card class="task task-item" shadow="hover">
					<h2>{{item.name}} 【{{ getTypeLabel(item.type) }}】</h2>
					<ul>
						<li>
							<h4>调用目标</h4>
							<p>{{item.target}}</p>
						</li>
						<li>
							<h4>定时规则</h4>
							<p>{{item.rule}}</p>
						</li>
					</ul>
					<div class="bottom">
						<div class="state">
							<el-tag v-if="item.status=='0'">正常</el-tag>
							<el-tag v-if="item.status=='1'" type="info">停用</el-tag>
						</div>
						<div class="handler">
							<el-popconfirm title="确定立即执行吗？" @confirm="run(item)">
								<template #reference>
									<el-button type="primary" icon="el-icon-caret-right" size="mini" circle></el-button>
								</template>
							</el-popconfirm>
							<el-dropdown trigger="click">
								<el-button type="primary" icon="el-icon-more" size="mini" circle plain></el-button>
								<template #dropdown>
									<el-dropdown-menu>
										<el-dropdown-item @click="edit(item)">编辑</el-dropdown-item>
										<el-dropdown-item @click="logs(item)">日志</el-dropdown-item>
										<el-dropdown-item @click="del(item)" divided>删除</el-dropdown-item>
									</el-dropdown-menu>
								</template>
							</el-dropdown>
						</div>
					</div>
				</el-card>
			</el-col>
			<el-col :xl="6" :lg="6" :md="8" :sm="12" :xs="24">
				<el-card class="task task-add" shadow="none" @click="add">
					<i class="el-icon-plus"></i>
					<p>添加计划任务</p>
				</el-card>
			</el-col>
		</el-row>
	</el-main>

	<save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false"></save-dialog>

	<el-drawer title="计划任务日志" v-model="dialog.logsVisible" :size="1000" direction="rtl" destroy-on-close>
		<logs ref="logList" />
	</el-drawer>
</template>

<script>
	import saveDialog from './save'
	import logs from './logs'

	export default {
		name: 'setting:crontab',
		
		components: {
			saveDialog,
			logs
		},

		provide() {
			return {
				list: this.list
			}
		},

		data() {
			return {
				dialog: {
					save: false,
					logsVisible: false
				},
				queryParams: {},
				list: [],
				types: ['', '命令任务', '类任务', 'URL任务', 'EVAL任务']
			}
		},

		mounted() {
			this.loadData()
		},

		methods: {

			// 载入数据
			async loadData() {
				await this.$API.crontab.getPageList(this.queryParams).then(res => {
					this.list = res.data
				})
			},

			// 获取类型标签
			getTypeLabel(key) {
				return this.types[key]
			},

			// 新增
			add(){
				this.dialog.save = true
				this.$nextTick(() => {
					this.$refs.saveDialog.open()
				})
			},

			// 编辑
			edit(row){
				this.dialog.save = true
				this.$nextTick(() => {
					this.$refs.saveDialog.open('edit').setData(row)
				})
			},

			// 删除
			del(row){
				this.$confirm(`确认删除 ${row.name} 定时任务吗？`,'提示', {
					type: 'warning',
					confirmButtonText: '删除',
					confirmButtonClass: 'el-button--danger'
				}).then(() => {
					this.$API.crontab.deletes(row.id).then(res => {
						if (res.success) {
							this.$message.success(res.message)
							this.loadData()
						} else {
							this.$message.success(res.message)
						}
					})
				}).catch(() => {})
			},
			
			// 定时任务日志
			logs(row){
				this.dialog.logsVisible = true
				this.$nextTick(() => {
					this.$refs.logList.setData(row)
				})
			},

			// 立刻执行定时任务
			run(row){
				this.$API.crontab.run({id: row.id}).then(() => {
					this.$message.success(`已成功执行计划任务：${row.name}`)
				})
			},

			//本地更新数据
			handleSuccess(data, mode){
				this.loadData()
			}
		}
	}
</script>

<style scoped>
	.task {height: 210px;}
	.task-item h2 {font-size: 15px;color: #3c4a54;padding-bottom:15px;}
	.task-item li {list-style-type:none;margin-bottom: 10px;}
	.task-item li h4 {font-size: 12px;font-weight: normal;color: #999;}
	.task-item li p {margin-top: 5px;}
	.task-item .bottom {border-top: 1px solid #EBEEF5;text-align: right;padding-top:10px;display: flex;justify-content: space-between;align-items: center;}

	.task-add {display: flex;flex-direction: column;align-items: center;justify-content: center;text-align: center;cursor: pointer;color: #999;}
	.task-add:hover {color: #409EFF;}
	.task-add i {font-size: 30px;}
	.task-add p {font-size: 12px;margin-top: 20px;}
</style>
