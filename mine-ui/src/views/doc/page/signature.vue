<template>
  <el-card shadow="never" class="card">
    <h2 style="line-height: 45px;">简易模式</h2>
        <el-card>
          <h2>身份信息</h2>
          <p><el-tag>MineAdmin API</el-tag> 的简易模式在请求时需要携带 <span class="tag">app_id</span>和<span class="tag">identity</span> 认证参数</p>
          <h2>生成identity</h2>
          <p>只需要对<span class="tag">app_id</span>和<span class="tag">app_secret</span> <el-tag>MD5</el-tag>即可</p>
        </el-card>
      
  </el-card>

  <el-card shadow="never" class="card" style="margin-top:15px">
    <h2 style="line-height: 45px;">复杂模式</h2>
    <el-timeline>
      <el-timeline-item timestamp="签名简介" placement="top">
        <el-card>
          <h2>签名简介</h2>
          <p><el-tag>MineAdmin API</el-tag> 的签名主要是用于获取身份令牌 <span class="tag">AccessToken</span> 时所需必要认证参数</p>
          <p>在请求需要复杂认证接口的时候，系统会验证 <span class="tag">AccessToken</span></p>
          <p>在请求获取 <span class="tag">AccessToken</span> 的接口时候，服务器会对用户请求合法性的 <span class="tag">signature</span> 进行检查，以此来确定是否向用户返回 <span class="tag">AccessToken</span></p>
        </el-card>
      </el-timeline-item>
      <el-timeline-item timestamp="获取app id 和 app secret" placement="top">
        <el-card>
          <h2>获取app id 和 app secret</h2>
          <p>进入后台管理系统后，依次点击以下导航获取<span class="tag">app id</span> 和 <span class="tag">app secret</span></p>
          <p><span class="tag">数据中心</span> > <span class="tag">应用中心</span> > <span class="tag">应用管理</span></p>
          <p>鼠标移动到要赋值的 <span class="tag">app id</span> 或 <span class="tag">app secret</span>，单击左键即可复制到剪切板</p>
        </el-card>
      </el-timeline-item>
      <el-timeline-item timestamp="生成signature" placement="top">
        <el-card>
          <h2>生成signature</h2>
          <p>一、组合签名数据格式</p>
          <ma-highlight :code="JSON.stringify(jsonData)" lang="json" />
          <p>二、根据字段名降序排序，组合<span class="tag">http原始字符串</span></p>
          <ma-highlight :code="demoData" lang="js" />
          <p>三、md5<span class="tag">http原始字符串</span>，完成生成 <el-tag type="danger">signature</el-tag></p>
          <ma-highlight :code="'2d41751369f4daaf9dd869aefa0da1e4'" lang="js" />
          
          
        </el-card>
      </el-timeline-item>
      <el-timeline-item timestamp="获取AccessToken" placement="top">
        <el-card>
          <h2>获取AccessToken</h2>
          <p>1. <span class="tag">AccessToken</span>有效期只有 <el-tag>7200</el-tag> 秒，获取后请缓存到本地</p>
          <p>2. 有效期内再次请求的<span class="tag">AccessToken</span>会导致之前请求的全部失效</p>
          <p>3. 最好使用服务器端来做接口请求，客户端可能会出现跨域等问题。</p>
          <p>4. 获取<span class="tag">AccessToken</span>地址：<el-tag>http://你的服务器地址/api/v1/getAccessToken</el-tag>请求方法：POST</p>
          <br />
          <p>组合完整请求参数</p>
          <ma-highlight :code="JSON.stringify(requestParams)" lang="json" />
          <p>请求获取<span class="tag">AccessToken</span>接口</p>
          <ma-highlight :code="'//请求方法：POST\nhttp://服务器地址/api/v1/getAccessToken'" lang="js" />
        </el-card>
      </el-timeline-item>
    </el-timeline>
  </el-card>
</template>

<script>
import maHighlight from '@/components/maHighlight'
export default {
  components: {
    maHighlight
  },
  data () {
    return {
      jsonData: {
        app_id: '到后台应用管理查看app id',
        app_secret: '到后台应用管理查看app secret',
        sign_ver: '1.0',
        timestamp: '当前系统时间戳',
      },
      demoData: 'app_id=xxxx&app_secret=xxxxxxxxxx&sign_ver=1.0&timestamp=128097733',
      requestParams: {
        app_id: 'xxxx',
        signature: '2d41751369f4daaf9dd869aefa0da1e4',
        timestamp: 128097733
      }
    }
  }
}
</script>

<style scoped>
.card {
    font-size: 14px;
}
h2 {
  margin: 0 0 10px 0;
}
p {
  line-height:32px;
}
.tag{
  color: #333;
  background: #f5f5f5;
  padding: 2px 8px;
  border-radius: 4px;
}
</style>