#!/usr/bin/env sh

set -e

set -x

# 运行migrate 和 seeder

php ../bin/hyperf.php migrate --seed

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

# 等待服务启动并进行健康检查
echo "Waiting for server to be ready..."
for i in $(seq 1 10); do
    sleep 2
    if kill -0 ${SERVER_PID} 2>/dev/null; then
        echo "Attempt $i: Server process is alive"
        # HTTP健康检查
        if curl -f http://localhost:9501 >/dev/null 2>&1; then
            echo "Server is responding to HTTP requests"
            break
        fi
    else
        echo "Server process died during startup"
        cat ${LOG_FILE}
        exit 1
    fi
done

# 检查服务是否启动成功
if kill -0 ${SERVER_PID} 2>/dev/null; then
    echo "MineAdmin server is running"
else
    echo "Failed to start MineAdmin server"
    exit 1
fi
