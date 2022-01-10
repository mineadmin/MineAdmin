import webSocket from "@/utils/webSocket"
import tool from '@/utils/tool'

class Message {

  ws

  timer = null

  interval = 10 * 1000

  constructor() {
    this.ws = new webSocket(
      process.env.VUE_APP_WS_URL + '?token=' + tool.getToken(), {
        onOpen:  _ => { console.log('已成功连接到消息服务器...') },
        onError: _ => { console.log('未成功连接到消息服务器...') },
        onClose: _ => { console.log('与消息服务器断开...') },
      }
    )
    
    this.ws.heartbeat.openHeartbeat = false
  }

  getMessage() {
    this.timer = setInterval(() => {
      this.ws.send({ event: 'get_unread_message' })
    }, this.interval)
  }

  connection() {
    this.ws.connection()
  }

}

export default Message