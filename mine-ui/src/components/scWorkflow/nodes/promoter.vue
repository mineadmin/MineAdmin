<template>
	<div class="node-wrap">
		<div class="node-wrap-box start-node" @click="show">
			<div class="title" style="background: #576a95;">
				<el-icon class="icon"><el-icon-user-filled /></el-icon>
				<span>{{ nodeConfig.nodeName }}</span>
			</div>
			<div class="content">
				<span>{{ toText(nodeConfig) }}</span>
			</div>
		</div>
		<add-node v-model="nodeConfig.childNode"></add-node>
		<el-drawer title="发起人" v-model="drawer" destroy-on-close append-to-body :size="500">
			<template #title>
				<div class="node-wrap-drawer__title">
					<label @click="editTitle" v-if="!isEditTitle">{{form.nodeName}}<el-icon class="node-wrap-drawer__title-edit"><el-icon-edit /></el-icon></label>
					<el-input v-if="isEditTitle" ref="nodeTitle" v-model="form.nodeName" clearable @blur="saveTitle" @keyup.enter="saveTitle"></el-input>
				</div>
			</template>
			<el-container>
				<el-main style="padding:0 20px 20px 20px">
					<el-form label-position="top">
						<el-form-item label="谁可以发起此审批">
							<el-button type="primary" icon="el-icon-plus" round @click="selectHandle(2, form.nodeRoleList)">选择角色</el-button>
							<div class="tags-list">
								<el-tag v-for="(role, index) in form.nodeRoleList" :key="role.id" type="info" closable @close="delRole(index)">{{role.name}}</el-tag>
							</div>
						</el-form-item>
						<el-alert v-if="form.nodeRoleList.length==0" title="不指定则默认所有人都可发起此审批" type="info" :closable="false"/>
					</el-form>
				</el-main>
				<el-footer>
					<el-button type="primary" @click="save">保存</el-button>
					<el-button @click="drawer=false">取消</el-button>
				</el-footer>
			</el-container>
		</el-drawer>
	</div>
</template>

<script>
	import addNode from './addNode'

	export default {
		inject: ['select'],
		props: {
			modelValue: { type: Object, default: () => {} }
		},
		components: {
			addNode
		},
		data() {
			return {
				nodeConfig: {},
				drawer: false,
				isEditTitle: false,
				form: {}
			}
		},
		watch:{
			modelValue(){
				this.nodeConfig = this.modelValue
			}
		},
		mounted() {
			this.nodeConfig = this.modelValue
		},
		methods: {
			show(){
				this.form = {}
				this.form = JSON.parse(JSON.stringify(this.nodeConfig))
				this.isEditTitle = false
				this.drawer = true
			},
			editTitle(){
				this.isEditTitle = true
				this.$nextTick(()=>{
					this.$refs.nodeTitle.focus()
				})
			},
			saveTitle(){
				this.isEditTitle = false
			},
			selectHandle(type, data){
				this.select(type, data)
			},
			delRole(index){
				this.form.nodeRoleList.splice(index, 1)
			},
			save(){
				this.$emit("update:modelValue", this.form)
				this.drawer = false
			},
			toText(nodeConfig){
				if(nodeConfig.nodeRoleList && nodeConfig.nodeRoleList.length > 0){
					return nodeConfig.nodeRoleList.map(item=>item.name).join("、")
				}else{
					return "所有人"
				}
			}
		}
	}
</script>

<style>
</style>
