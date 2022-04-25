// 端口
const port = process.env.npm_config_port || 2800

// 基础url
const base_url = process.env.VUE_APP_URL

// 代理API前缀
const proxy_api = process.env.VUE_APP_API

module.exports = {

  //设置为空打包后不分更目录还是多级目录
  publicPath: '',
  //build编译后存放静态文件的目录
  //assetsDir: "static",

  // build编译后不生成资源MAP文件
  productionSourceMap: false,

  //开发服务,build后的生产模式还需nginx代理
  devServer: {
    host: '0.0.0.0',
    open: true, //运行后自动打开游览器
    port: port, //挂载端口
    proxy: {
      [proxy_api]: {
        target: base_url,
        ws: true,
        pathRewrite: {
          ['^' + proxy_api]: '/'
        }
      }
    }
  },

  chainWebpack: config => {
    // 移除 prefetch 插件
    config.plugins.delete('preload');
    config.plugins.delete('prefetch');
    config.resolve.alias.set('vue-i18n', 'vue-i18n/dist/vue-i18n.cjs.js');
  },

  configureWebpack: config => {
    //性能提示
    config.performance = {
      hints: false
    }
    config.optimization = {
      splitChunks: {
        chunks: "async",
        automaticNameDelimiter: '~',
        name: true,
        cacheGroups: {
          //第三方库抽离
          vendor: {
            name: "modules",
            test: /[\\/]node_modules[\\/]/,
            priority: -10
          },
          elicons: {
            name: "elicons",
            test: /[\\/]node_modules[\\/]@element-plus[\\/]icons[\\/]/
          },
          tinymce: {
            name: "tinymce",
            test: /[\\/]node_modules[\\/]tinymce[\\/]/
          },
          monacoEditor: {
            name: "monaco-editor",
            test: /[\\/]node_modules[\\/]monaco-editor[\\/]/
          },
          echarts: {
            name: "echarts",
            test: /[\\/]node_modules[\\/]echarts[\\/]/
          },
          xgplayer: {
            name: "xgplayer",
            test: /[\\/]node_modules[\\/]xgplayer.*[\\/]/
          }
        }
      }
    }
  }

}
