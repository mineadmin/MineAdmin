#!/bin/bash

# 启动项目
function startProject {
  echo 111
}

# 停止项目
function stopProject {
  echo 222
}

# 重启项目
function restartProject {
  echo 333
}

# 显示状态
function showStatus {
  echo 444
}

# 帮助
function showHelp {
  echo 555
}

case "$1" in
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
      bilingual "命令不存在: $1" "Unknown command: $1"
      showHelp
      exit 1
    fi
    ;;
esac