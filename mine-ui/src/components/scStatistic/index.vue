<!--
 * @Descripttion: 统计数值组件
 * @version: 1.0
 * @Author: sakuya
 * @Date: 2021年6月23日13:11:32
 * @LastEditors:
 * @LastEditTime:
-->

<template>
	<div class="sc-statistic">
		<div class="sc-statistic-title">
			{{ title }}
			<el-tooltip v-if="tips" effect="light">
				<template #content>
					<div style="width: 200px;line-height: 2;">
						{{ tips }}
					</div>
				</template>
				<el-icon class="sc-statistic-tips"><el-icon-question-filled/></el-icon>
			</el-tooltip>
		</div>
		<div class="sc-statistic-content">
			<span v-if="prefix" class="sc-statistic-content-prefix">{{ prefix }}</span>
			<span class="sc-statistic-content-value">{{ cmtValue }}</span>
			<span v-if="suffix" class="sc-statistic-content-suffix">{{ suffix }}</span>
		</div>
		<div v-if="description || $slots.default" class="sc-statistic-description">
			<slot>
			{{ description }}
			</slot>
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			title: { type: String, required: true, default: "" },
			value: { type: String, required: true, default: "" },
			prefix: { type: String, default: "" },
			suffix: { type: String, default: "" },
			description: { type: String, default: "" },
			tips: { type: String, default: "" },
			groupSeparator: { type: Boolean, default: false }
		},
		data() {
			return {

			}
		},
		computed: {
			cmtValue(){
				return this.groupSeparator ? this.$TOOL.groupSeparator(this.value) : this.value
			}
		}
	}
</script>

<style scoped>
	.sc-statistic-title {font-size: 12px;color: #999;margin-bottom: 10px;display: flex;align-items: center;}
	.sc-statistic-tips {margin-left: 5px;}
	.sc-statistic-content {font-size: 20px;color: #333;}
	.sc-statistic-content-value {font-weight: bold;}
	.sc-statistic-content-prefix {margin-right: 5px;}
	.sc-statistic-content-suffix {margin-left: 5px;font-size: 12px;}
	.sc-statistic-description {margin-top: 10px;color: #999;}

	[data-theme='dark'] .sc-statistic-content {color: #d0d0d0;}
</style>
