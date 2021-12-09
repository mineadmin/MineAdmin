<template>
	 <div class="add-node-btn-box">
	 	<div class="add-node-btn">
			<el-popover placement="right-start" :width="270" v-model:visible="visible" :hide-after="0" :show-after="0">
				<template #reference>
					<el-button type="primary" icon="el-icon-plus" circle></el-button>
				</template>
				<div class="add-node-popover-body">
					<ul>
						<li>
							<el-icon style="color: #ff943e;" @click="addType(1)"><el-icon-user-filled /></el-icon>
							<p>审批节点</p>
						</li>
						<li>
							<el-icon style="color: #3296fa;" @click="addType(2)"><el-icon-promotion /></el-icon>
							<p>抄送节点</p>
						</li>
						<li>
							<el-icon style="color: #15BC83;" @click="addType(4)"><el-icon-share /></el-icon>
							<p>条件分支</p>
						</li>
					</ul>
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
						type: 1,			//节点类型
						setType: 1,			//审核人类型
						nodeUserList: [],	//审核人成员
						nodeRoleList: [],	//审核角色
						examineLevel: 1,	//指定主管层级
						directorLevel: 1,	//自定义连续主管审批层级
						selectMode: 1,		//发起人自选类型
						termAuto: false,	//审批期限超时自动审批
						term: 0,			//审批期限
						termMode: 1,		//审批期限超时后执行类型
						examineMode: 1,		//多人审批时审批方式
						directorMode: 0,	//连续主管审批方式
						childNode: this.modelValue
					}
				}else if(type == 2){
					node = {
						nodeName: "抄送人",
						type: 2,
						userSelectFlag: true,
						nodeUserList: [],
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
								priorityLevel: 1,
								conditionMode: 1,
								conditionList: []
							},
							{
								nodeName: "条件2",
								type: 3,
								priorityLevel: 2,
								conditionMode: 1,
								conditionList: []
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
