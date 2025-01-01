import { ElMessage, ElMessageBox, ElNotification } from 'element-plus'
import type { MessageBoxInputValidator } from 'element-plus/es/components/message-box/src/message-box.type'

export function useMessage() {
  const t = useTrans().globalTrans
  return {
    // 消息提示
    info(content: string) {
      ElMessage.info(content)
    },
    // 错误消息
    error(content: string) {
      ElMessage.error(content)
    },
    // 成功消息
    success(content: string) {
      ElMessage.success(content)
    },
    // 警告消息
    warning(content: string) {
      ElMessage.warning(content)
    },
    // 弹出提示
    alert(content: string) {
      ElMessageBox.alert(content, t('crud.confirmTitle'))
    },
    // 错误提示
    alertError(content: string) {
      ElMessageBox.alert(content, t('crud.confirmTitle'), { type: 'error' })
    },
    // 成功提示
    alertSuccess(content: string) {
      ElMessageBox.alert(content, t('crud.confirmTitle'), { type: 'success' })
    },
    // 警告提示
    alertWarning(content: string) {
      ElMessageBox.alert(content, t('crud.confirmTitle'), { type: 'warning' })
    },
    // 通知提示，第二个参数对象可配置所有参数
    notify(content: string, args: Record<string, any> = {}) {
      ElNotification({
        title: t('crud.confirmTitle'),
        message: content,
        type: 'info',
        ...args,
      })
    },
    // 错误通知
    notifyError(content: string) {
      ElNotification.error({
        title: t('crud.confirmTitle'),
        message: content,
      })
    },
    // 成功通知
    notifySuccess(content: string) {
      ElNotification.success({
        title: t('crud.confirmTitle'),
        message: content,
      })
    },
    // 警告通知
    notifyWarning(content: string) {
      ElNotification.warning({
        title: t('crud.confirmTitle'),
        message: content,
      })
    },
    // 确认框
    confirm(content: string, tip?: string) {
      return ElMessageBox.confirm(content, tip || t('crud.confirmTitle'), {
        confirmButtonText: t('crud.ok'),
        cancelButtonText: t('crud.cancel'),
        type: 'warning',
      })
    },
    // 删除框
    delConfirm(content?: string, tip?: string) {
      return ElMessageBox.confirm(
        content || t('crud.delMessage'),
        tip || t('crud.confirmTitle'),
        {
          confirmButtonText: t('crud.ok'),
          cancelButtonText: t('crud.cancel'),
          type: 'error',
        },
      )
    },
    // 导出框
    exportConfirm(content?: string, tip?: string) {
      return ElMessageBox.confirm(
        content || t('crud.exportMessage'),
        tip || t('crud.confirmTitle'),
        {
          confirmButtonText: t('crud.ok'),
          cancelButtonText: t('crud.cancel'),
          type: 'warning',
        },
      )
    },
    // 提交框
    prompt(content: string, defaultValue?: string, tip?: string, inputValidator?: MessageBoxInputValidator) {
      return ElMessageBox.prompt(content, tip || t('crud.confirmTitle'), {
        confirmButtonText: t('crud.ok'),
        cancelButtonText: t('crud.cancel'),
        inputValue: defaultValue,
        inputValidator,
        type: 'warning',
      })
    },
  }
}
