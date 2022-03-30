<template>
	<div class="sceditor">
		<Editor v-model="contentValue" :init="init" :disabled="disabled" :placeholder="placeholder" @onClick="onClick" />
	</div>
</template>

<script>
	import API from "@/api";
	import Editor from '@tinymce/tinymce-vue'
	import tinymce from 'tinymce/tinymce'
	import 'tinymce/themes/silver'
	import 'tinymce/icons/default'

	// 引入编辑器插件
	import 'tinymce/plugins/code'  //编辑源码
	import 'tinymce/plugins/image'  //插入编辑图片
	import 'tinymce/plugins/link'  //超链接
	import 'tinymce/plugins/preview'//预览
	import 'tinymce/plugins/table'  //表格

	export default {
		components: {
			Editor
		},
		props: {
			modelValue: {
				type: String,
				default: ""
			},
			placeholder: {
				type: String,
				default: ""
			},
			height: {
				type: Number,
				default: 300,
			},
			disabled: {
				type: Boolean,
				default: false
			},
			plugins: {
				type: [String, Array],
				default: 'code image link preview table'
			},
			toolbar: {
				type: [String, Array],
				default: 'undo redo |  forecolor backcolor bold italic underline strikethrough link | formatselect fontselect fontsizeselect | \
					alignleft aligncenter alignright alignjustify outdent indent lineheight | bullist numlist | \
					image table  preview | code selectall'
			}
		},
		data() {
			return {
				init: {
					language_url: 'tinymce/langs/zh_CN.js',
					language: 'zh_CN',
					skin_url: 'tinymce/skins/ui/oxide',
					content_css: "tinymce/skins/content/default/content.css",
					menubar: false,
					statusbar: true,
					plugins: this.plugins,
					toolbar: this.toolbar,
					fontsize_formats: '12px 14px 16px 18px 20px 22px 24px 28px 32px 36px 48px 56px 72px',
					height: this.height,
					placeholder: this.placeholder,
					branding: false,
					resize: true,
					elementpath: true,
					content_style: "",
					images_upload_handler: async (blobInfo, success, failure) => {
						const data = new FormData();
						data.append('image', blobInfo.blob() ,blobInfo.filename());
						try {
							const res = await API.upload.uploadImage(data)
							success(this.$TOOL.viewImage(res.data.url))
						}catch (error) {
							failure("图片上传失败")
						}
					},
					setup: function(editor) {
						editor.on('init', function() {
							this.getBody().style.fontSize = '14px';
						})

					}
				},
				contentValue: this.modelValue
			}
		},
		watch: {
			modelValue(val) {
				this.contentValue = val
			},
			contentValue(val){
				this.$emit('update:modelValue', val);
			}
		},
		mounted() {
			tinymce.init({})
		},
		methods: {
			onClick(e){
				this.$emit('onClick', e, tinymce)
			}
		}
	}
</script>

<style>
</style>
