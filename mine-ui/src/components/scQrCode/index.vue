<!--
 * @Descripttion: 生成二维码组件
 * @version: 1.0
 * @Author: sakuya
 * @Date: 2021年12月20日14:22:20
 * @LastEditors:
 * @LastEditTime:
-->

<template>
	<img ref="img"/>
</template>

<script>
	import QRcode from "qrcodejs2"

	export default {
		props: {
			text: { type: String, required: true, default: "" },
			size: { type: Number, default: 100 },
			logo: { type: String, default: "" },
			logoSize: { type: Number, default: 30 },
			logoPadding: { type: Number, default: 5 },
			colorDark: { type: String, default: "#000000" },
			colorLight: { type: String, default: "#ffffff" },
			correctLevel: { type: Number, default: 2 },
		},
		data() {
			return {
				qrcode: null
			}
		},
		watch:{
			text(){
				this.draw()
			}
		},
		mounted() {
			this.draw()
		},
		methods: {
			//创建原始二维码DOM
			async create(){
				return new Promise((resolve) => {
					var element = document.createElement("div");
					new QRcode(element, {
						text: this.text,
						width: this.size,
						height: this.size,
						colorDark: this.colorDark,
						colorLight: this.colorLight,
						correctLevel: this.correctLevel
					})
					if (element.getElementsByTagName("canvas")[0]) {
						this.qrcode = element
						resolve()
					}
				})
			},
			//绘制LOGO
			async drawLogo(){
				return new Promise((resolve) => {
					var logo = new Image()
					logo.src = this.logo
					const logoPos = (this.size - this.logoSize) / 2
					const rectSize = this.logoSize + this.logoPadding
					const rectPos = (this.size - rectSize) / 2
					var ctx = this.qrcode.getElementsByTagName("canvas")[0].getContext("2d")
					logo.onload = ()=>{
						ctx.fillRect(rectPos, rectPos, rectSize, rectSize)
						ctx.drawImage(logo, logoPos, logoPos, this.logoSize, this.logoSize)
						resolve()
					}
				})
			},
			async draw(){
				await this.create()
				if(this.logo){
					await this.drawLogo()
				}
				this.$refs.img.src = this.qrcode.getElementsByTagName("canvas")[0].toDataURL("image/png")
			},
		}
	}
</script>

<style>
</style>
