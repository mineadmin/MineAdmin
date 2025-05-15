#!/bin/bash

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