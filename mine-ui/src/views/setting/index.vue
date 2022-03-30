<template>
	<el-main>
		<el-card shadow="never">
			
			<el-button
				type="success"
				plain
				@click="clearCache"
				icon="el-icon-delete"
			>清除缓存</el-button>

			<el-tabs tab-position="top">

				<el-tab-pane label="系统组配置">
					<el-form ref="systemForm" :model="systemForm" label-width="100px" style="margin-top: 20px; width: 600px;">

						<el-form-item
							:label="item.name"
							:prop="item.key"
							v-for="(item, index) in systemConfig"
							:key="index"
						>
							<!-- 文本框类 -->
							<el-input
								v-model="systemForm[item.key]"
								v-if="item.key !== 'site_storage_mode'"
								:type="item.key == 'site_desc' || item.key == 'site_copyright' ? 'textarea' : 'text'"
								:rows="3"
								maxlength="255"
								show-word-limit
								clearable
							/>
							<!-- 下拉框类 -->
							<el-select v-model="systemForm[item.key]" v-else style="width: 100%;">
								<el-option
									v-for="(mode, index) in uploadMode"
									:key="index"
									:value="mode.value"
									:label="mode.label"
								/>
							</el-select>
							<div class="el-form-item-msg">{{ item.remark }}</div>
						</el-form-item>

						<el-form-item>
							<el-button type="primary" @click="saveSystemConfig">保 存</el-button>
						</el-form-item>
					</el-form>
				</el-tab-pane>

				<el-tab-pane label="扩展组配置">

					<el-alert
						title="扩展配置为系统业务所有的配置，应该由系统管理员操作，如需用户配置应另起业务配置页面。"
						type="warning" style="margin-bottom: 15px;"
					/>

					<el-button
						type="primary"
						icon="el-icon-plus"
						@click="add"
					>新增配置</el-button>

					<el-table :data="extendConfig" stripe>
						<el-table-column label="#" type="index" width="50"></el-table-column>
						<el-table-column label="配置Key" prop="key" width="150">
							<template #default="scope">
								<el-input v-if="scope.row.isSet" v-model="scope.row.key" placeholder="请输入配置Key"></el-input>
								<span v-else>{{scope.row.key}}</span>
							</template>
						</el-table-column>
						<el-table-column label="配置Value" prop="value" width="350">
							<template #default="scope">
								<template v-if="scope.row.isSet">
									<el-input v-model="scope.row.value" placeholder="请输入配置Value"></el-input>
								</template>
								<span v-else>{{scope.row.value}}</span>
							</template>
						</el-table-column>
						<el-table-column label="配置名称" prop="name" width="350">
							<template #default="scope">
								<el-input v-if="scope.row.isSet" v-model="scope.row.name" placeholder="请输入配置名称"></el-input>
								<span v-else>{{scope.row.name}}</span>
							</template>
						</el-table-column>
						<el-table-column label="备注说明" prop="remark" width="150">
							<template #default="scope">
								<el-input v-if="scope.row.isSet" v-model="scope.row.remark" placeholder="请输入备注说明"></el-input>
								<span v-else>{{scope.row.remark}}</span>
							</template>
						</el-table-column>
						<el-table-column min-width="1"></el-table-column>
						<el-table-column label="操作" fixed="right" width="100">
							<template #default="scope">
								<el-button @click="save(scope.row, scope.$index)" type="text" size="small">{{scope.row.isSet ? '保存' : '修改' }}</el-button>
								<el-popconfirm title="确定删除吗？" @confirm="del(scope.row, scope.$index)">
									<template #reference>
										<el-button type="text" size="small">删除</el-button>
									</template>
								</el-popconfirm>
							</template>
						</el-table-column>
					</el-table>
				</el-tab-pane>

			</el-tabs>
		</el-card>
	</el-main>
</template>

<script>
	export default {
		name: 'system',
		data() {
			return {
				systemConfig: [],
				extendConfig: [],

				uploadMode: [],

				systemForm: {},
				extendForm: {},
			}
		},

		created () {
			this.getSystemConfig()
			this.getExtendConfig()
			this.getDictData()
		},

		methods: {
			
			// 系统配置组
			async getSystemConfig () {
				await this.$API.config.getSystemConfig().then(res => {
					this.systemConfig = res.data
					this.systemConfig.map(item => {
						this.systemForm[item.key] = item.value
					})
				})
			},

			// 扩展配置组
			async getExtendConfig() {
				await this.$API.config.getExtendConfig().then(res => {
					this.extendConfig = res.data
				})
			},

			// 字典
			async getDictData() {
				await this.getDict('upload_mode').then(res => {
					this.uploadMode = res.data
				})
			},

			// 保存系统类配置
			saveSystemConfig() {
				this.$API.config.saveSystemConfig(this.systemForm).then(res => {
					res.success && this.$message.success(res.message)
				})
			},

			clearCache () {
				this.$API.config.clearCache().then(() => {
					this.$message.success('缓存已清除')
				})
			},

			add(){
				this.extendConfig.push({
					key: '',
					value: '',
					name: '',
					remark: '',
					isVirtual: true,
					isSet: true
				})
			},

			save(row){
				console.log(row.isSet)
				// 保存
				if(row.isSet){
					row.isSet = false
					// 编辑
					if (row.isEdit) {
						this.$API.config.update(row).then(res => {
							this.$message.success(res.message)
							this.getExtendConfig()
						})
					} else {
					// 保存
						row.group_name = 'extend'
						this.$API.config.save(row).then(res => {
							this.$message.success(res.message)
							this.getExtendConfig()
						})
					}
				}else{
					row.isSet = true
					row.isEdit = true
				}
			},

			del(row, index){
				// 虚拟字段，未保存的直接删除
				if (row.isVirtual) {
					this.extendConfig.splice(index, 1)
				} else {
					this.$API.config.delete(row.key).then(res => {
						this.$message.success(res.message)
						this.getExtendConfig()
					})
				}
			},
		}
	}
</script>

<style>
</style>
