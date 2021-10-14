<template>
	 <div class="add-node-btn-box">
	 	<div class="add-node-btn">
			<el-popover placement="right-start" :width="300" v-model:visible="visible" :hide-after="0" :show-after="0">
				<template #reference>
					<el-button type="primary" icon="el-icon-plus" circle></el-button>
				</template>
				<div class="add-node-popover-body">
					<el-button icon="el-icon-user-solid" type="primary" circle plain @click="addType(1)"></el-button>
					<el-button icon="el-icon-s-promotion" type="primary" circle plain @click="addType(2)"></el-button>
					<el-button icon="el-icon-share" type="primary" circle plain @click="addType(4)"></el-button>
				</div>
			</el-popover>
	 	</div>
	 </div>
</template>

<script>
	export default {
		props: {
			modelValue: { type: Object, default: () => {} }
		},
		data() {
			return {
				visible: false
			}
		},
		mounted() {

		},
		methods: {
			addType(type){
				var node = {}
				if (type == 1) {
					node = {
						nodeName: "审核人",
						type: 1,
						childNode: this.modelValue
					}
				}else if(type == 2){
					node = {
						nodeName: "抄送人",
						type: 2,
						childNode: this.modelValue
					}

				}else if(type == 4){
					node = {
						nodeName: "条件路由",
						type: 4,
						conditionNodes: [
							{
								nodeName: "条件1",
								type: 3,
								priorityLevel: 1
							},
							{
								nodeName: "条件2",
								type: 3,
								priorityLevel: 2
							}
						],
						childNode: this.modelValue
					}

				}
				this.$emit("update:modelValue", node)
				this.visible = false
			}
		}
	}
</script>

<style>
</style>
