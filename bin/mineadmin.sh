#!/bin/bash

checkEvn() {
  # 检查config/config.yaml文件是否存在
  if [ ! -f ".env" ]; then
      if [ -f ".env.example" ]; then
          echo "提示：.env 环境变量文件不存在，正在从示例文件复制..."
          cp .env.example .env
          echo "已成功复制示例 .env.example 作为 .env 环境变量文件"
      else
          echo "安装错误：.env.example 和 .env 文件都不存在！"
          echo "请加入QQ群反馈该信息：150105478"
          return 1
      fi
  fi

  return 0
}

# 安装项目
installProject() {



  echo 'install'
}

# 启动项目
startProject() {
  echo 111
}

# 停止项目
stopProject() {
  echo 222
}

# 重启项目
restartProject() {
  echo 333
}

# 显示状态
showStatus() {
  echo 444
}

# 帮助
showHelp() {
  echo "/---------------------- welcome to use -----------------------\\"
  echo "|               _                ___       __          _      |"
  echo "|    ____ ___  (_)___  _____    /   | ____/ /___ ___  (_)___  |"
  echo "|   / __ \`__ \/ / __ \/ ___/   / /| |/ __  / __ \`__ \/ / __ \ |"
  echo "|  / / / / / / / / / / /__/   / ___ / /_/ / / / / / / / / / / |"
  echo "| /_/ /_/ /_/_/_/ /_/\___/   /_/  |_\__,_/_/ /_/ /_/_/_/ /_/  |"
  echo "|                                                             |"
  echo "\____________________  Copyright MineAdmin ___________________|"
  echo ""
  echo "用法: bash $0 [命令]"
  echo ""
  echo "命令:"
  echo "  install           安装MineAdmin"
  echo "  start             启动所有服务"
  echo "  stop              停止所有服务"
  echo "  restart           重启所有服务"
  echo "  status            显示服务状态"
  echo ""
  echo "未提供命令，默认使用 start 启动所有服务"
}

case "$1" in
  install)
    installProject
    ;;
  start)
    startProject
    ;;
  stop)
    stopProject
    ;;
  restart)
    restartProject
    ;;
  status)
    showStatus
    ;;
  help|--help|-h)
    showHelp
    ;;
  *)
    if [ -z "$1" ]; then
      startProject
    else
      echo "命令不存在: $1"
      echo ""
      showHelp
      exit 1
    fi
    ;;
esac