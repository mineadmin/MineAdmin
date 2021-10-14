<template>
	<el-container>
		<el-main  style="padding:0 20px;">
			<maTable
				ref="table"
				:api="api"
				:autoLoad="false"
				stripe
			>
				<el-table-column label="执行时间" prop="created_at" width="160"></el-table-column>
				<el-table-column label="执行结果" prop="state" width="80">
					<template #default="scope">
						<span v-if="scope.row.status=='0'" style="color: #67C23A;"><i class="el-icon-success"></i> 成功</span>
						<span v-else style="color: #F56C6C;"><i class="el-icon-error"></i> 异常</span>
					</template>
				</el-table-column>
				<el-table-column label="执行目标" prop="target" width="200" :show-overflow-tooltip="true"></el-table-column>
				<el-table-column label="异常信息" prop="logs" width="100" fixed="right">
					<template #default="scope">
						<el-button size="mini" @click="show(scope.row)" type="text">查看</el-button>
					</template>
				</el-table-column>
			</maTable>
		</el-main>

		<el-drawer title="异常信息" v-model="logsVisible" :size="500" direction="rtl" destroy-on-close>
			<el-main style="padding:0 20px 20px 20px;">
				<pre>{{ log == '' ? '无异常信息' : log }}</pre>
			</el-main>
		</el-drawer>
	</el-container>
</template>

<script>
	export default {
		data() {
			return {
				logsVisible: false,
				api: {
					list: this.$API.crontab.getLogPageList
				},
				queryParams: {
					crontab_id: undefined
				},
				log: '',
			}
		},

		methods: {

			show(row){
				this.logsVisible = true;
				this.log = row.exception_info
			},

			setData(row) {
				this.queryParams.crontab_id = row.id
				this.$refs.table.upData(this.queryParams)
			}

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
