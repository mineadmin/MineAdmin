/**
 * websocket处理封装
 * @author X.Mo <root@imoi.cn>
 */

 class webSocket
 {
   /**
    * websocket 连接句柄
    * @var Websocket
    */
   ws = null
 
   /**
    * 服务器地址
    * @var
    */
   serverUrl
 
   /**
    * 心跳配置
    * @var object
    */
   heartbeat = {
     openHeartbeat: true,    // 是否开启心跳
     interval: 10 * 1000     // 心跳发送间隔，默认10秒
   }
 
   /**
    * 重连配置
    * @var object
    */
   reconnectConfig = {
     isReconnect: true,    // 是否开启短线重连
     timer: null,          // 计时器对象
     interval: 5 * 1000,   // 重连间隔时间
     retry: 10,            // 重连次数
   }
 
   /**
    * ws事件
    * @var Array
    */
   onEvent = []
 
   /**
    * 原生 websocket 事件
    */
   events = {
     onOpen:  e => {},
     onClose: e => {},
     onError: e => {},
   }
 
   /**
    * 构造函数
    * @param {string} url
    * @param {object} events
    */
   constructor(url, events) {
     this.serverUrl = url
     this.events = Object.assign(this.events, events)
   }
 
   /**
    * 绑定事件
    */
   on(ev, callback) {
     this.onEvent[ev] = callback
   }
 
   /**
    * 连接websocket
    */
   connection() {
     if (this.ws) {
       this.ws.close()
       this.ws = null
     }
 
     this.ws = new WebSocket(this.serverUrl)
     this.ws.onopen = this.onOpen.bind(this)
     this.ws.onerror = this.onError.bind(this)
     this.ws.onmessage = this.onMessage.bind(this)
     this.ws.onclose = this.onClose.bind(this)
   }
 
   /**
    * 解析数据
    *
    * @param {Object} evt Websocket 消息
    */
   onParseData(evt) {
     return JSON.parse(evt.data)
   }
 
   /**
    * 打开连接
    *
    * @param {Object} evt Websocket 消息
    */
    onOpen(evt) {
     this.events.onOpen(evt)
     this.sendHeartbeat()
   }
 
   /**
    * 关闭连接
    *
    * @param {Object} evt Websocket 消息
    */
   onClose(evt) {
     clearInterval(this.heaerbeatTimer)
 
     if (evt.code == 1006) {
       this.reconnect()
     }
 
     this.events.onClose(evt)
   }
 
   /**
    * 连接错误
    *
    * @param {Object} evt Websocket 消息
    */
   onError(evt) {
     this.events.onError(evt)
   }
 
   /**
    * 接收消息
    *
    * @param {Object} evt Websocket 消息
    */
   onMessage(evt) {
     let result = this.onParseData(evt)
 
     this.onEvent.hasOwnProperty(result.event) && this.onEvent[result.event](result.message, result.data)
     this.onEvent.hasOwnProperty(result.event) || console.warn(`WebSocket 消息事件[${result.event}]未绑定...`)
   }
 
   /**
    * 向服务器发送心跳数据包
    */
   sendHeartbeat() {
     if (this.heartbeat.openHeartbeat) {
       setInterval(() => {
         this.ws.send('PONG')
       }, this.heartbeat.interval)
     }
   }
 
   /**
    * 短线重连
    */
   reconnect() {
     let reconnect = this.reconnectConfig
     if (! reconnect.isReconnect || reconnect.retry == 0) {
       return
     }
 
     this.reconnectConfig.isReconnect = false
 
     reconnect.timer && clearTimeout(reconnect.timer)
 
     this.reconnectConfig.timer = setTimeout(_ => {
       this.connection()
       this.reconnectConfig.isReconnect = false
       this.reconnectConfig.retry--
       console.log( `网络连接已断开，正在尝试重新连接(${this.reconnectConfig.retry})...`)
     }, reconnect.interval)
   }
 
   /**
    * 向服务器发送数据
    *
    * @param {Object} data
    */
   send(data) {
     this.ws && this.ws.send(JSON.stringify(data))
   }
 
   /**
    * 关闭连接
    */
   close(){
     this.ws.close()
     this.ws = null
   }
 }
 
 export default webSocket
 