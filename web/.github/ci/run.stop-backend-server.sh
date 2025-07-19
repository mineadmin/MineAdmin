#!/usr/bin/env sh

set -e

set -x

# 停止 MineAdmin 后端服务
LOG_FILE="mineadmin_server.log"
PID_FILE="mineadmin_server.pid"

# 检查 PID 文件是否存在
if [ -f ${PID_FILE} ]; then
    SERVER_PID=$(cat ${PID_FILE})
    echo "Stopping MineAdmin server with PID: ${SERVER_PID}"
    # 停止服务
    if kill -0 ${SERVER_PID} 2>/dev/null; then
        kill ${SERVER_PID}
        echo "MineAdmin server stopped successfully"
    else
        echo "MineAdmin server is not running or already stopped"
    fi
    # 删除 PID 文件
    rm -f ${PID_FILE}
else
    echo "PID file not found, MineAdmin server may not be running"
fi