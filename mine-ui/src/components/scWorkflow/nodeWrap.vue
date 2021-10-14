<template>
	<promoter v-if="nodeConfig.type==0" v-model="nodeConfig"></promoter>

	<approver v-if="nodeConfig.type==1" v-model="nodeConfig"></approver>

	<send v-if="nodeConfig.type==2" v-model="nodeConfig"></send>

	<branch v-if="nodeConfig.type==4" v-model="nodeConfig">
		<template v-slot="slot">
			<node-wrap v-if="slot.node" v-model="slot.node.childNode"></node-wrap>
		</template>
	</branch>

	<node-wrap v-if="nodeConfig.childNode" v-model="nodeConfig.childNode"></node-wrap>

</template>

<script>
	import approver from './nodes/approver'
	import promoter from './nodes/promoter'
	import branch from './nodes/branch'
	import send from './nodes/send'

	export default {
		props: {
			modelValue: { type: Object, default: () => {} }
		},
		components: {
			approver,
			promoter,
			branch,
			send
		},
		data() {
			return {
				nodeConfig: {},
			}
		},
		watch:{
			modelValue(val){
				this.nodeConfig = val
			},
			nodeConfig(val){
				this.$emit("update:modelValue", val)
			}
		},
		mounted() {
			this.nodeConfig = this.modelValue
		},
		methods: {

		}
	}
</script>

<style>
</style>
