# MineAdmin 安全审计报告

## 1. 审计范围与结论

- **项目**：MineAdmin，Hyperf 3.2，PHP 8.2+
- **范围**：`app/`、`plugin/mine-admin/app-store/`、`config/`、依赖锁文件及 Docker 部署文件
- **方法**：静态代码审计、配置审计、依赖漏洞扫描（`composer audit --locked`）
- **时间**：2026-09-15

本次审计发现 9 项需要处理的问题，其中插件上传链可形成服务器端任意代码执行，属于最高优先级；当前工作区还存在真实数据库/JWT/商店令牌，必须立即轮换。未进行生产环境黑盒测试，以下“可利用性”以代码路径和配置为依据。

## 2. 漏洞列表

| 编号 | 等级 | 问题 | 影响 |
| --- | --- | --- | --- |
| MA-01 | **严重** | 本地插件 ZIP 安装可执行任意 PHP/命令 | 获得应用进程权限后执行任意代码 |
| MA-02 | **高** | 工作区及 Compose 暴露硬编码凭据、数据库/Redis 网络暴露 | 数据库接管、JWT 伪造、商店账户滥用 |
| MA-03 | **高** | Guzzle 依赖存在已知安全公告 | 受影响 HTTP/重定向场景下的主机混淆、Cookie/代理及 DoS 风险 |
| MA-04 | **高** | 插件市场下载 ZIP 未做安全解压 | 可信源被劫持或响应被篡改时可发生路径穿越写文件 |
| MA-05 | **中** | 终端任务状态/日志/取消缺少任务归属校验（IDOR） | 掌握任务号的低权限用户可读取日志或取消他人任务 |
| MA-06 | **中** | Bearer/JWT 允许通过 URL 查询参数传递 | 令牌进入访问日志、代理、浏览器历史及 Referer |
| MA-07 | **中** | 调试模式返回异常堆栈并开放 Swagger/CORS | 泄露源码路径、调用栈和接口信息，扩大攻击面 |
| MA-08 | **中** | 重置密码使用固定默认值 `123456` | 账号被重置后可被猜测登录 |
| MA-09 | **中** | `composer.json` 与 `composer.lock` 大面积不一致 | 实际运行版本与声明版本不同，安全修复不可控 |

## 3. 详细发现

### MA-01：插件上传安装导致任意代码执行（严重）

**证据**：

- `plugin/mine-admin/app-store/src/Controller/IndexController.php:88-93` 的 `uploadLocalApp` 仅由权限码保护，接收上传文件后直接进入安装流程。
- `plugin/mine-admin/app-store/src/Service/Service.php:114-148` 解压 ZIP 后只校验 `mine.json` 的 `name` 和 ZIP 路径安全性，没有拒绝 `composer.files`、`composer.classMap`、`composer.installScript`、`composer.script` 等可执行字段。
- `vendor/mineadmin/app-store/src/Plugin.php:155-160` 在安装开始即调用 `loadPlugin`，会加载插件声明的 PHP 文件/类；`213-225` 实例化并执行 `installScript`，并通过 `System::exec` 执行 Composer 脚本；`264` 起还执行迁移/Seeder。

**影响与验证**：拥有 `plugin:store:upload` 权限的账号上传包含恶意 `mine.json` 和 PHP 文件的 ZIP，即可在 PHP worker 身份下执行代码，读取 `.env`、数据库或写入后门。该权限不应被视为普通文件上传权限。

**修复**：生产环境禁用本地插件上传；若必须保留，应采用签名/可信发布者白名单，严格 schema 校验并删除所有脚本、动态类加载和任意 `files` 字段，安装放入隔离容器/低权限进程，安装前后进行完整性校验。

### MA-02：凭据泄露及基础设施不安全暴露（高）

**证据**：

- `.env:5-24` 含内网数据库地址、`DB_USERNAME=root`、明文数据库密码、固定 `JWT_SECRET` 和 `MINE_ACCESS_TOKEN`。
- `docker-compose.yml:10-12` 发布 Redis `6379`，`31-35` 发布 MySQL `3306` 且使用 `MYSQL_ROOT_PASSWORD: root`；Redis 未配置认证。
- `config/autoload/jwt.php:21-23` 直接使用环境变量作为全站 HMAC 签名密钥。

**影响**：任何能读取工作区、镜像层、日志或同网段端口的攻击者都可能连接数据库/Redis；JWT 密钥泄露后可伪造任意用户令牌（包括超级管理员）；商店令牌泄露后可被滥用调用官方商店接口。

**修复**：立即轮换数据库密码、JWT 密钥和商店令牌并吊销旧令牌；凭据改用密钥管理系统注入，禁止将 `.env` 打入镜像或日志；Compose 仅绑定 `127.0.0.1`/内部网络，使用非 root 数据库账号和 Redis ACL/密码，生产环境关闭调试。

### MA-03：Guzzle 及 PSR-7 依赖存在已知漏洞（高）

**扫描结果**：`composer audit --locked --no-interaction` 报告 11 条公告，涉及：

- `guzzlehttp/guzzle` **7.12.0**：受影响于 7.15.1/7.15.2 之前的多项主机混淆、Cookie、代理授权、HTTPS 降级和响应 Cookie DoS 公告。
- `guzzlehttp/psr7` **2.12.0**：受影响于 2.12.1/2.12.3 之前的 CRLF 注入及主机校验问题。
- `laminas/laminas-mime` 被标记为 abandoned。

**修复**：在兼容性验证后升级到 Composer 审计建议的最新安全版本（至少 `guzzlehttp/guzzle >=7.15.2`、`guzzlehttp/psr7 >=2.12.3`），重新生成并提交 `composer.lock`，在 CI 中强制执行 `composer audit`。

### MA-04：市场下载 ZIP 未安全解压（高）

**证据**：`vendor/mineadmin/app-store/src/Service/Impl/AppStoreServiceImpl.php:128-142` 将远程响应保存为 ZIP，并直接调用 `extractTo(Plugin::PLUGIN_PATH . '/' . explode('/', $identifier)[0])`，未检查 ZIP 条目中的 `../`、绝对路径或符号链接。

**影响**：官方市场、代理链路或下载响应被攻陷时，恶意 ZIP 可写出插件目录，覆盖应用文件或配置；结合后续插件加载可升级为 RCE。当前自定义上传流程虽有路径检查，但市场下载路径没有复用该防护。

**修复**：下载后使用统一的 ZIP 条目校验（拒绝目录穿越、绝对路径、符号链接），解压到临时隔离目录，校验签名/哈希后再原子移动；下载客户端强制 HTTPS、限制响应大小和重定向目标。

### MA-05：终端任务对象级越权（中）

**证据**：`plugin/mine-admin/app-store/src/Service/TerminalTaskService.php:61-74` 的 `status`、`logs`、`cancel` 只检查权限码；`TerminalRuntimeStore` 按任务号读取文件，但没有比较任务 `created_by` 与当前用户。

**影响**：任务号一旦从前端、日志或监控中泄露，拥有终端基础权限的用户可读取其他用户的命令输出（可能包含路径、包源、错误信息），或取消他人的安装任务。

**修复**：读取、日志和取消均要求 `created_by === currentUser->id()`；超级管理员单独放行。若任务设计为全局可见，应显式定义角色范围并记录审计日志，而不是默认放开。

### MA-06：JWT 允许出现在 URL（中）

**证据**：`vendor/mineadmin/auth-jwt/Middleware/AbstractTokenMiddleware.php` 的 `getToken()` 在 `Authorization`/`token` 请求头之外，还从查询参数 `?token=` 读取令牌。

**影响**：URL 会被 Web/反向代理访问日志、浏览器历史、监控系统和 Referer 记录，导致长期 Bearer 令牌泄露。

**修复**：删除 query-string 取 token 的兼容逻辑，仅接受 `Authorization: Bearer`（或受保护的 HttpOnly、Secure、SameSite Cookie）；同时在网关和日志系统中清理历史 URL 令牌。

### MA-07：调试错误、Swagger 和跨域配置暴露（中）

**证据**：

- `.env:1-3` 当前设置 `APP_ENV=dev`、`APP_DEBUG=true`；`app/Exception/Handler/AbstractHandler.php:47-58、88-96` 在调试模式将异常文件、行号和 trace 写入响应，并设置 `Access-Control-Allow-Origin: *`。
- `config/autoload/swagger.php:14-20` 永久启用 Swagger，监听 `0.0.0.0:9503`（`config/autoload/server.php:25` 及 `docker-compose.yml:46-48`）。Swagger HTTP server 不经过业务 JWT 中间件。

**影响**：未授权访问者可枚举完整接口和参数；触发异常即可获取服务器路径与调用栈。若调试配置误用于生产，信息泄露会显著降低利用门槛。

**修复**：生产强制 `APP_DEBUG=false`、`APP_ENV=prod`；异常响应只返回通用错误和 request-id；关闭 Swagger 或置于内网并增加认证；不要使用通配符凭据跨域，按受信任来源白名单配置 CORS。

### MA-08：固定默认密码（中）

**证据**：`app/Model/Permission/User.php:116-119` 的 `resetPassword()` 将密码设置为固定值 `123456`；`app/Http/Admin/Controller/Permission/UserController.php:87-100` 通过带 `id` 的请求触发重置。

**影响**：具备 `permission:user:password` 权限的操作者重置账号后，目标账号进入可预测凭据状态；若目标用户未及时修改，攻击者可直接登录。该默认值也出现在测试/演示文档中。

**修复**：服务端生成一次性随机临时密码或强制密码重置令牌，设置短过期时间并要求首次登录修改；对重置目标实施数据权限和二次确认，禁止将固定密码写入代码。

### MA-09：依赖锁文件与声明不一致（中）

**证据**：`composer validate --no-check-publish` 报告锁文件不是最新状态：`composer.json` 声明 Hyperf/MineAdmin 3.2.x，而锁文件包含大量 3.1.x/3.0.x；`doctrine/dbal` 也声明 `^4.4` 但锁定为 3.10.5。

**影响**：部署时可能继续使用旧的、未满足声明约束的代码，导致已修复漏洞未实际生效，也会使审计和 SBOM 结果失真。

**修复**：在干净环境执行 `composer update`（或按包更新）并提交与 `composer.json` 一致的 `composer.lock`；CI 中执行 `composer validate --strict`、`composer install --prefer-dist --no-dev` 和 `composer audit`，禁止锁文件漂移。

## 4. 修复优先级

1. 立即禁用/隔离插件上传，轮换 `.env` 中所有凭据和 Compose 默认密码。
2. 修复市场 ZIP 安全解压并升级 Guzzle 依赖。
3. 增加终端任务归属校验，移除 URL token 支持。
4. 关闭生产调试、Swagger 公网端口及宽松 CORS，替换固定密码重置流程。
5. 在 CI 中加入 `composer audit`、secret scanning、ZIP 安全测试和权限/IDOR 集成测试。

## 5. 未覆盖事项

本报告未连接实际生产数据库、Redis、反向代理或前端应用，未验证网络 ACL、TLS、WAF、备份和运行时容器隔离。上线前应进行经授权的黑盒测试和依赖升级回归测试。
