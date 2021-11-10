<template>
  <div class="code-container">
    <pre class="ma-highlight hljs" v-html="highlightHTML" />
    <el-button type="primary" class="copy-btn" @click="copy">复制</el-button>
  </div>
</template>

<script>
// 相关文档
// https://highlightjs.org/usage/
// http://highlightjs.readthedocs.io/en/latest/api.html#configure-options
import useClipboard from 'vue-clipboard3'
import highlight from 'highlight.js'
import htmlFormat from './libs/htmlFormat'
import './libs/style.github.css'
export default {
  name: 'maHighlight',
  props: {
    code: {
      type: String,
      required: false,
      default: ''
    },
    formatHtml: {
      type: Boolean,
      required: false,
      default: false
    },
    lang: {
      type: String,
      required: false,
      default: ''
    }
  },
  data () {
    return {
      highlightHTML: ''
    }
  },
  mounted () {
    this.highlight()
  },
  watch: {
    code () {
      this.highlight()
    }
  },
  methods: {
    async copy() {
      try {
        await useClipboard().toClipboard(this.code)
        this.$message.success(this.$t('sys.copy_success'))
      } catch(e) {
        this.$message.error(this.$t('sys.copy_fail'))
      }
    },
    highlight () {
      let code = this.formatHtml ? htmlFormat(this.code) : this.code
      code = this.lang === 'json' ? this.formatJson(code) : code
      this.highlightHTML = highlight.highlightAuto(code, [
        this.lang,
        'html',
        'javascript',
        'json',
        'css',
        'scss',
        'less'
      ]).value
    },
    transitionJsonToString (jsonObj, callback) {
      // 转换后的jsonObj受体对象
      let _jsonObj = null;
      // 判断传入的jsonObj对象是不是字符串，如果是字符串需要先转换为对象，再转换为字符串，这样做是为了保证转换后的字符串为双引号
      if (Object.prototype.toString.call(jsonObj) !== "[object String]") {
        try {
          _jsonObj = JSON.stringify(jsonObj);
        } catch (error) {
          // 转换失败错误信息
          console.error('您传递的json数据格式有误，请核对...');
          console.error(error);
          callback(error);
        }
      } else {
        try {
          if (jsonObj == '' || jsonObj == null) {
            return '{}'
          }
          jsonObj = jsonObj.replace(/(\')/g, '\"');
          _jsonObj = JSON.stringify(JSON.parse(jsonObj));
        } catch (error) {
          // 转换失败错误信息
          console.error('您传递的json数据格式有误，请核对...');
          console.error(error);
          callback(error);
        }
      }
      return _jsonObj;
    },
    formatJson (jsonObj, callback) {
      // 转换后的字符串变量
      let formatted = '';
      // 换行缩进位数
      let pad = 0;
      // 一个tab对应空格位数
      let PADDING = '    ';
      // json对象转换为字符串变量
      let jsonString = this.transitionJsonToString(jsonObj, callback);
      if (!jsonString) {
        return jsonString;
      }
      // 存储需要特殊处理的字符串段
      let _index = [];
      // 存储需要特殊处理的“再数组中的开始位置变量索引
      let _indexStart = null;
      // 存储需要特殊处理的“再数组中的结束位置变量索引
      let _indexEnd = null;
      // 将jsonString字符串内容通过\r\n符分割成数组
      let jsonArray = [];
        // 正则匹配到{,}符号则在两边添加回车换行
      jsonString = jsonString.replace(/([\{\}])/g, '\r\n$1\r\n');
      // 正则匹配到[,]符号则在两边添加回车换行
      jsonString = jsonString.replace(/([\[\]])/g, '\r\n$1\r\n');
      // 正则匹配到,符号则在两边添加回车换行
      jsonString = jsonString.replace(/(\,)/g, '$1\r\n');
      // 正则匹配到要超过一行的换行需要改为一行
      jsonString = jsonString.replace(/(\r\n\r\n)/g, '\r\n');
      // 正则匹配到单独处于一行的,符号时需要去掉换行，将,置于同行
      jsonString = jsonString.replace(/\r\n\,/g, ',');
      // 特殊处理双引号中的内容
      jsonArray = jsonString.split('\r\n');
      jsonArray.forEach(function (node, index) {
        // 获取当前字符串段中"的数量
        let num = node.match(/\"/g) ? node.match(/\"/g).length : 0;
        // 判断num是否为奇数来确定是否需要特殊处理
        if (num % 2 && !_indexStart) {
          _indexStart = index
        }
        if (num % 2 && _indexStart && _indexStart != index) {
          _indexEnd = index
        }
        // 将需要特殊处理的字符串段的其实位置和结束位置信息存入，并对应重置开始时和结束变量
        if (_indexStart && _indexEnd) {
          _index.push({
            start: _indexStart,
            end: _indexEnd
          })
          _indexStart = null
          _indexEnd = null
        }
      })
      // 开始处理双引号中的内容，将多余的"去除
      _index.reverse().forEach(function (item) {
        let newArray = jsonArray.slice(item.start, item.end + 1)
        jsonArray.splice(item.start, item.end + 1 - item.start, newArray.join(''))
      })
      // 奖处理后的数组通过\r\n连接符重组为字符串
      jsonString = jsonArray.join('\r\n');
      // 将匹配到:后为回车换行加大括号替换为冒号加大括号
      jsonString = jsonString.replace(/\:\r\n\{/g, ':{');
      // 将匹配到:后为回车换行加中括号替换为冒号加中括号
      jsonString = jsonString.replace(/\:\r\n\[/g, ':[');
      // 将上述转换后的字符串再次以\r\n分割成数组
      jsonArray = jsonString.split('\r\n');
        // 将转换完成的字符串根据PADDING值来组合成最终的形态
        jsonArray.forEach(function (item) {
          let i = 0;
            // 表示缩进的位数，以tab作为计数单位
          let indent = 0;
          // 表示缩进的位数，以空格作为计数单位
          let padding = '';
          if (item.match(/\{$/) || item.match(/\[$/)) {
            // 匹配到以{和[结尾的时候indent加1
            indent += 1
          } else if (item.match(/\}$/) || item.match(/\]$/) || item.match(/\},$/) || item.match(/\],$/)) {
            // 匹配到以}和]结尾的时候indent减1
            if (pad !== 0) {
              pad -= 1
            }
          } else {
              indent = 0
          }
          
          for (i = 0; i < pad; i++) {
            padding += PADDING
          }
          formatted += padding + item + '\r\n'
          pad += indent
        })
      // 返回的数据需要去除两边的空格
      return formatted.trim();
    }
  }
}
</script>

<style scoped>
.code-container {
  position: relative;
}
.copy-btn {
  position: absolute;
  top: 20px;
  right: 20px;
}
.ma-highlight {
  margin: 0px;
  border-radius: 4px;
}
pre {
  padding: 20px !important;
  font-family: Consolas;
  font-size: 12px;
  line-height: 18px;
}
</style>
