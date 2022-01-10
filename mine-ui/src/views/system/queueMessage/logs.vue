<template>
	<el-container>
		<el-main  style="padding:0 20px;">
			<el-button
				type="danger"
				plain
				icon="el-icon-delete"
				:disabled="selection.length==0"
				@click="batchDel"
			>删除</el-button>
			<maTable
				ref="table"
				row-key="id"
				:api="api"
				:autoLoad="false"
				@selection-change="selectionChange"
				stripe
			>
				<el-table-column type="selection" width="50"></el-table-column>
				<el-table-column label="内容类型" prop="content_type" width="100"></el-table-column>
				<el-table-column label="标题" prop="title" width="200"></el-table-column>
				<el-table-column label="消息内容" prop="content" width="200"></el-table-column>
				<el-table-column label="接收人" prop="receive_name" width="100"></el-table-column>
				<el-table-column label="发送人" prop="send_name" width="100"></el-table-column>
				<el-table-column
					label="发送状态"
					prop="send_status"
					width="100"
				>
					<template #default="scope">
						<ma-dict-tag  :options="message_send_status_data" :value="scope.row.send_status" />
					</template>

				</el-table-column>

				<el-table-column
					label="查看状态"
					prop="read_status"
					width="80"
				>
					<template #default="scope">
						<ma-dict-tag  :options="message_read_status_data" :value="scope.row.read_status" />
					</template>

				</el-table-column>
<!--				<el-table-column label="备注" prop="remark" width="160"></el-table-column>-->

			</maTable>
		</el-main>
	</el-container>
</template>

<script>
	export default {
		async created() {
			await this.getDictData();
		},
		data() {
			return {
				selection: [],
				logsVisible: false,
				api: {
					list: this.$API.systemQueueMessage.getLogList,
				},
				queryParams: {
					send_by: undefined
				},
				log: '',
				send_by: '',
				message_send_status_data: [],
				message_read_status_data: []
			}
		},

		methods: {
			getData(){
				this.api.list = this.$API.systemQueueMessage.getLogList;
			},
			show(row){
				this.logsVisible = true;
				this.log = row.exception_info
			},

			setData(row) {
				this.send_by = row.send_by
				this.queryParams.send_by = row.send_by
				this.$refs.table.upData(this.queryParams)
			},

			// 获取字典数据
			getDictData() {

				this.getDict('message_send_status').then(res => {
					this.message_send_status_data = res.data
				})
				this.getDict('message_read_status').then(res => {
					this.message_read_status_data = res.data
				})
			},

			//批量删除
			async batchDel(){
				await this.$confirm(`确定删除选中的 ${this.selection.length} 项吗？`, '提示', {
				type: 'warning'
				}).then(() => {
				const loading = this.$loading();
				let ids = []
				this.selection.map(item => ids.push(item.id))
					this.$API.systemQueueMessage.deletes(ids.join(',')).then(() => {
					this.$refs.table.upData()
				})
				loading.close()
				this.$message.success("操作成功")
				})
			},

			//表格选择后回调事件
			selectionChange(selection){
				this.selection = selection;
			},

		}
	}
</script>

<style>
pre {
	font-size: 12px;
	color: #ccc;
	padding:20px;
	background: #333;
	font-family: consolas;
	line-height: 1.5;
	overflow: auto;
	border-radius: 4px;
}
</style>
