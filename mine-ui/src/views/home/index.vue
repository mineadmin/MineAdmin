<template>
	<div v-if="pageLoading">
		<el-main>
			<el-card shadow="never">
				<el-skeleton :rows="1"></el-skeleton>
			</el-card>
			<el-card shadow="never" style="margin-top: 15px;">
				<el-skeleton></el-skeleton>
			</el-card>
		</el-main>
	</div>
	<widgets v-if="dashboard === 'statistics' " @on-mounted="onMounted"></widgets>
	<work v-else @on-mounted="onMounted"></work>
</template>

<script>
	import { defineAsyncComponent } from 'vue';
	const work = defineAsyncComponent(() => import('./work'));
	const widgets = defineAsyncComponent(() => import('./widgets'));

	export default {
		name: "dashboard",
		components: {
			work,
			widgets
		},
		data(){
			return {
				pageLoading: true,
				dashboard: '0'
			}
		},
		created(){
			this.dashboard = this.$TOOL.data.get("user").user.dashboard;
		},
		mounted(){

		},
		methods: {
			onMounted(){
				this.pageLoading = false
			}
		}
	}
</script>

<style>
</style>
