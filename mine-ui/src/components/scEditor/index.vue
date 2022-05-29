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
  // import 'tinymce/models/dom'

  // 引入编辑器插件
  import 'tinymce/plugins/code'  //编辑源码
	import 'tinymce/plugins/image'  //插入编辑图片
	import 'tinymce/plugins/media'  //插入视频
	import 'tinymce/plugins/link'  //超链接
	import 'tinymce/plugins/preview'//预览
	import 'tinymce/plugins/template'//模板
	import 'tinymce/plugins/table'  //表格
	import 'tinymce/plugins/pagebreak'  //分页
	import 'tinymce/plugins/lists'  //列
	import 'tinymce/plugins/advlist'  //列
	import 'tinymce/plugins/quickbars'  //快速工具条

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
        default: 500,
      },
      disabled: {
        type: Boolean,
        default: false
      },
      plugins: {
        type: [String, Array],
        default: 'code image media link preview table quickbars template pagebreak lists advlist'
      },
      toolbar: {
        type: [String, Array],
        default: () => [
          'undo redo forecolor backcolor bold italic underline strikethrough link alignleft aligncenter alignright alignjustify outdent indent',
          'fontsize numlist bullist pagebreak image media table template preview code meeting'
        ]
      }
    },
    data() {
      return {
        init: {
          language_url: 'tinymce/langs/zh_CN.js',
          language: 'zh_CN',
          skin_url: 'tinymce/skins/ui/oxide',
          content_css: "tinymce/skins/content/default/content.css",
          menubar: true,
          statusbar: true,
          plugins: this.plugins,
          toolbar: this.toolbar,
          font_size_formats: '12px 14px 16px 18px 22px 24px 36px 72px',
          height: this.height,
          placeholder: this.placeholder,
          branding: false,
          resize: true,
          elementpath: true,
          content_style: "",
          quickbars_selection_toolbar: 'forecolor backcolor bold italic underline strikethrough link',
          quickbars_image_toolbar: 'alignleft aligncenter alignright',
          quickbars_insert_toolbar: false,
          image_caption: true,
          image_advtab: true,
          images_upload_handler: function(blobInfo) {
            return new Promise((resolve, reject) => {
              const data = new FormData();
              data.append("image", blobInfo.blob() ,blobInfo.filename());
              API.upload.uploadImage(data).then((res) => {
                resolve(this.$TOOL.viewImage(res.data.url))
              }).catch(() => {
                reject("Image upload failed")
              })
            })
          },
          setup: (editor) => {
            tinymce.PluginManager.add('meeting', () =>{
              editor.on('init', () => {
                editor.getBody().style.fontSize = '14px';
                editor.ui.registry.addButton('meeting', {
                  text:'套用模板',
                  icon: 'title',
                  onAction: () => {
                      editor.windowManager.openUrl({
                          title:"选择会议模板",
                          url:'https://www.163.com',
                          width:840,
                          height:300
                      });
                  }
                })
              })
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
