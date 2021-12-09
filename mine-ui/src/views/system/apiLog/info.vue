<template>

	<el-main style="padding:0 20px;">
		<el-descriptions :column="1" border size="small">
			<el-descriptions-item label="接口名称">{{data.api_name}}</el-descriptions-item>
			<el-descriptions-item label="请求接口">{{data.access_name}}</el-descriptions-item>
			<el-descriptions-item label="状态代码">{{data.response_code}}</el-descriptions-item>
			<el-descriptions-item label="请求时间">{{data.access_time}}</el-descriptions-item>
			<el-descriptions-item label="用户IP">{{data.ip}}</el-descriptions-item>
			<el-descriptions-item label="用户地点">{{data.ip_location}}</el-descriptions-item>
		</el-descriptions>
		<el-collapse v-model="activeNames" style="margin-top: 20px;" accordion>
			<el-collapse-item title="请求数据" name="request">
				<ma-highlight :code="data.request_data" lang="json" />
			</el-collapse-item>
			<el-collapse-item title="响应数据" name="response">
				<ma-highlight :code="data.response_data" lang="json" />
			</el-collapse-item>
		</el-collapse>
	</el-main>

</template>

<script>
	import maHighlight from '@/components/maHighlight'
	export default {
		components: {
			maHighlight
		},
		data() {
			return {
				data: {},
				activeNames: ['request'],
			}
		},
		methods: {
			setData(data){
				this.data = data
			}
		}
	}
</script>
