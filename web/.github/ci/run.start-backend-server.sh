#!/usr/bin/env sh

set -e

set -x

# 启动 MineAdmin 后端服务在后台，保存 PID 和输出
LOG_FILE="mineadmin_server.log"
PID_FILE="mineadmin_server.pid"

# 启动服务并保存 PID
php ../bin/hyperf.php start > ${LOG_FILE} 2>&1 &
SERVER_PID=$!

# 保存 PID 到文件
echo ${SERVER_PID} > ${PID_FILE}

echo "MineAdmin server started with PID: ${SERVER_PID}"
echo "Log file: ${LOG_FILE}"
echo "PID file: ${PID_FILE}"

# 等待服务启动
sleep 3

# 检查服务是否启动成功
if kill -0 ${SERVER_PID} 2>/dev/null; then
    echo "MineAdmin server is running"
else
    echo "Failed to start MineAdmin server"
    exit 1
fi
