<!--
 * @Descripttion: 弹窗扩展组件
 * @version: 1.0
 * @Author: sakuya
 * @Date: 2021年8月27日08:51:52
 * @LastEditors:
 * @LastEditTime:
-->

<template>
	<div class="sc-dialog" ref="scDialog">
	<el-dialog ref="dialog" v-model="dialogVisible" :fullscreen="isFullscreen" v-bind="$attrs" :show-close="false">
		<template #title>
			<slot name="title">
				<span class="el-dialog__title">{{ title }}</span>
			</slot>
			<div class="sc-dialog__headerbtn">
				<button v-if="showFullscreen" aria-label="fullscreen" type="button" @click="setFullscreen">
					<el-icon v-if="isFullscreen" class="el-dialog__close"><el-icon-bottom-left /></el-icon>
					<el-icon v-else class="el-dialog__close"><el-icon-full-screen /></el-icon>
				</button>
				<button v-if="showClose" aria-label="close" type="button" @click="closeDialog">
					<el-icon class="el-dialog__close"><el-icon-close /></el-icon>
				</button>
			</div>
		</template>
		<div v-loading="loading">
			<slot></slot>
		</div>
		<template #footer>
			<slot name="footer"></slot>
		</template>
	</el-dialog>
	</div>
</template>

<script>
	export default {
		props: {
			modelValue: { type: Boolean, default: false },
			title: { type: String, default: "" },
			showClose: { type: Boolean, default: true },
			showFullscreen: { type: Boolean, default: true },
			drag: { type: Boolean, default: true },
			loading: { type: Boolean, default: false }
		},
		data() {
			return {
				dialogVisible: false,
				isFullscreen: false
			}
		},
		watch:{
			modelValue(){
				this.dialogVisible = this.modelValue
				if(this.dialogVisible){
					this.$refs.scDialog.querySelector('.el-dialog').style.top = '0px'
					this.$refs.scDialog.querySelector('.el-dialog').style.left = '0px'
					this.isFullscreen = false
				}
			}
		},
		mounted() {
			this.dialogVisible = this.modelValue
			this.drag && this.dialogdrag()
		},
		methods: {
			//关闭
			closeDialog(){
				this.dialogVisible = false
			},
			//最大化
			setFullscreen(){
				this.isFullscreen = !this.isFullscreen
			},
			//绑定拖拽
			dialogdrag(){
				const dialogHeaderEl = this.$refs.scDialog.querySelector('.el-dialog__header')
				const dragDom = this.$refs.scDialog.querySelector('.el-dialog')
				//dialogHeaderEl.style.cursor = 'move'

				dialogHeaderEl.onmousedown = (e) => {
					const disX = e.clientX - dialogHeaderEl.offsetLeft
					const disY = e.clientY - dialogHeaderEl.offsetTop
					const screenWidth = document.body.clientWidth
					const screenHeight = document.documentElement.clientHeight
					const dragDomWidth = dragDom.offsetWidth
					const dragDomheight = dragDom.offsetHeight
					let minDragDomLeft = -dragDom.offsetLeft
					let maxDragDomLeft = screenWidth - dragDom.offsetLeft - dragDomWidth
					let minDragDomTop = -dragDom.offsetTop
					let maxDragDomTop = screenHeight - dragDom.offsetTop - dragDomheight

					if(screenHeight < dragDomheight){
						return false
					}
					dragDom.style.marginBottom = '0px'

					let styL = getComputedStyle(dragDom).left
					let styT = getComputedStyle(dragDom).top
					if(styL.includes('%')) {
						styL = +document.body.clientWidth * (+styL.replace('%', '') / 100)
						styT = +document.body.clientHeight * (+styT.replace('%', '') / 100)
					}else {
						styL = +styL.replace('px', '')
						styT = +styT.replace('px', '')
					}
					document.onmousemove = function (e) {
						let left = e.clientX - disX
						let top = e.clientY - disY
						if (left < minDragDomLeft) {
							left = minDragDomLeft
						} else if (left > maxDragDomLeft) {
							left = maxDragDomLeft
						}
						if (top < minDragDomTop) {
							top = minDragDomTop
						} else if (top > maxDragDomTop) {
							top = maxDragDomTop
						}
						dragDom.style.cssText += `;left:${left + styL}px;top:${top + styT}px;`
					}
					document.onmouseup = function () {
						document.onmousemove = null
						document.onmouseup = null
					}
				}
			}
		}
	}
</script>

<style scoped>
	.sc-dialog__headerbtn {position: absolute;top: var(--el-dialog-padding-primary);right: var(--el-dialog-padding-primary);}
	.sc-dialog__headerbtn button {padding: 0;background: transparent;border: none;outline: none;cursor: pointer;font-size: var(--el-message-close-size,16px);margin-left: 15px;color: var(--el-color-info);}
	.sc-dialog__headerbtn button:hover .el-dialog__close {color: var(--el-color-primary);}
	.sc-dialog:deep(.el-dialog).is-fullscreen {display: flex;flex-direction: column;top:0px !important;left:0px !important;}
	.sc-dialog:deep(.el-dialog).is-fullscreen .el-dialog__header {}
	.sc-dialog:deep(.el-dialog).is-fullscreen .el-dialog__body {flex:1;overflow: auto;}
	.sc-dialog:deep(.el-dialog).is-fullscreen .el-dialog__footer {padding-bottom: 10px;border-top: 1px solid var(--el-border-color-base);}
</style>
