# 未来1.0版介绍
目前在全力开发 `1.0 release` 版本 （不支持老版本升级）。

新版很多大的调整，最大的变化是前端重构，使用了 `Arco Design` UI 库，替换了 `SCUI` 框架，在这里需要感谢`SCUI`框架，不然可能就没有`MineAdmin`了。

如果仅仅是换了UI库就没什么大不了，但新版本使用了最新的技术栈：Vue3 + Pinia + Vite + Arco + Js

没错，没有使用ts
- 第一个我不会ts，也不愿意学（估计很多人一样）
- 第二个，ts太麻烦了。 ts 确实有他的好处，但这里有个人喜好原因在里面

这次前端日常使用的组件都是自己开发，像图片、文件和分块上传、用户选择器、资源选择器、大表哥CRUD（表格crud）以及 全新的ui，完全为`MineAdmin`量身定制

这里着重介绍一下自主开发 `大表哥crud` 系统

开发及使用方面学习了 `Avue` 的表格系统思路（非代码抄袭），感谢 `Avue` 作者开发出这么牛X的组件

`表格Crud` 能让使用 `Vue` 回到过去 `layui` 配置化做功能感觉，只需要会写 `json` 配置 `key` 和 `value` 就可以几分钟之内让前端完成对后端的对接联调，并且完成以下但不包括：

- 增、删、改、查（每个可配置权限）
- 一般列表数据展示
- 树形数据展示
- 列表搜索功能
- 表格和列的动态配置功能
- 支持所有的`Arco` 数据输入组件
- 支持所有 `MineAdmin` 内置组件
- 列表页面，都有相应的`插槽`支持，方便自定义自己想要的功能


# 项目介绍

<p align="center">
    <img src="https://doc.mineadmin.com/logo.svg" width="120" />
</p>
<p align="center">
    <a href="https://www.mineadmin.com" target="_blank">官网</a> |
    <a href="https://doc.mineadmin.com" target="_blank">文档</a> | 
    <a href="https://demo.mineadmin.com" target="_blank">演示</a> |
    <a href="https://hyperf.wiki/2.2/#/" target="_blank">Hyperf官方文档</a> 
</p>

<p align="center">
    <img src="https://gitee.com/xmo/MineAdmin/badge/star.svg?theme=dark" />
    <img src="https://gitee.com/xmo/MineAdmin/badge/fork.svg?theme=dark" />
    <img src="https://svg.hamm.cn/badge.svg?key=License&value=Apache-2.0&color=da4a00" />
    <img src="https://svg.hamm.cn/badge.svg?key=MineAdmin&value=v0.7.1" />
</p>
PHP有很多优秀的后台管理系统，但基于Swoole的后台管理系统没找到合适我自己的。
所以就开发了一套后台管理系统。系统可以用于网站管理后台、CMS、CRM、OA、ERP等。

系统基于Hyperf框架开发，前端使用Vue3.0 + SCUI（基于Element UI），也支持PC和移动端。企业和个人可以免费使用。

如果觉着还不错的话，就请点个 ⭐star 支持一下吧，这将是对我最大的支持和鼓励！

- 腾讯云特惠专场：[点击进入](http://txy.mineadmin.com)
- 阿里云特惠专场：[点击进入](http://aly.mineadmin.com)

## 内置功能

1.  用户管理，完成用户添加、修改、删除配置，支持不同用户登录后台看到不同的首页
2.  部门管理，部门组织机构（公司、部门、小组），树结构展现支持数据权限
3.  岗位管理，可以给用户配置所担任职务
4.  角色管理，角色菜单权限分配、角色数据权限分配
5.  菜单管理，配置系统菜单和按钮等
6.  字典管理，对系统中经常使用并且固定的数据可以重复使用和维护
7.  系统配置，系统的一些常用设置管理
8.  操作日志，用户对系统的一些正常操作的查询
9.  登录日志，用户登录系统的记录查询
10. 在线用户，查看当前登录的用户
11. 服务监控，查看当前服务器状态和PHP环境等信息
12. 依赖监控，查看当前程序所依赖的库信息和版本
13. 附件管理，管理当前系统上传的文件及图片等信息
14. 数据表维护，对系统的数据表可以进行清理碎片和优化
15. 模块管理，管理系统当前所有模块
16. 数据表设计器，简单版数据库设计器，搭配代码生成器事半功倍
17. 定时任务，在线（添加、修改、删除)任务调度包含执行结果日志
18. 代码生成，前后端代码的生成（php、vue、js、sql），支持下载和生成到模块
19. 缓存监控，查看Redis信息和系统所使用key的信息
20. API管理，对应用和接口管理、接口授权等功能。接口文档自动生成，输入、输出参数检查等
21. 队列管理，消息队列管理功能、消息管理、消息发送。使用ws方式即时消息提醒（需安装rabbitMQ）

## 环境需求

- Swoole >= 4.6.x 并关闭 `Short Name`
- PHP === 8.0 并开启以下扩展：
    - mbstring
    - json
    - pdo
    - openssl
    - redis
    - pcntl
- Mysql >= 5.7
- Redis >= 4.0


## 下载项目
- MineAdmin没有使用SQL文件导入安装，系统使用Migrates迁移文件形式安装和填充数据，请知悉。

- 项目下载，请确保已经安装了 `Composer`
```shell
git clone https://gitee.com/xmo/MineAdmin && cd MineAdmin
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
composer install
```

## 后端安装
 - 项目安装需要开两个终端，一个启动项目，一个执行安装命令

打开启动终端，启动项目
```shell
php bin/hyperf.php start
```
切换到安装终端，执行安装命令，完成`.env`文件的配置
```shell
php bin/hyperf.php mine:install
```
待提示以下信息后，切换到启动终端，重启项目，加载`.env`配置信息
```shell
Reset the ".env" file. Please restart the service before running 
the installation command to continue the installation.
```
切换到安装终端，再次执行安装命令，执行Migrates数据迁移文件和SQL数据填充，完成安装。
```shell
php bin/hyperf.php mine:install
```

## 前端安装

请先确保安装了node.js，yarn 或者 npm 工具，建议使用yarn
```shell
cd mine-ui && yarn
or
cd mine-ui && npm install
```
启动
```shell
yarn dev
or
npm run dev
```

## 体验地址

[体验地址](https://demo.mineadmin.com)
- 账号：superAdmin
- 密码：admin123

> 请勿添加脏数据

## QQ群

> <img src="https://img.shields.io/badge/Q群-15169734-red.svg" />

## 鸣谢

> 以下排名不分先后

[hyperf 一款高性能企业级协程框架](https://hyperf.io/)

[SCUI 中后台前端解决方案](https://gitee.com/lolicode/scui)

[swoole PHP协程框架](https://www.swoole.com)

[Element Plus 桌面端组件库](https://element-plus.gitee.io/zh-CN/)

[Jetbrains 生产力工具](https://www.jetbrains.com/)

## 演示图片
<table>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4n3tA.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nKmD.png"></td>
    </tr>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4n1kd.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nQTH.png"></td>
    </tr>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nM0e.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4n8fI.png"></td>
    </tr>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nJpt.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nY1P.png"></td>
    </tr>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nt6f.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nNX8.png"></td>
    </tr>
     <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nw7Q.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nanS.png"></td>
    </tr>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nBkj.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nrhn.png"></td>
    </tr>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nDts.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nypq.png"></td>
    </tr>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4n610.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4ngXT.png"></td>
    </tr>
    <tr>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nccV.png"></td>
        <td><img src="https://z3.ax1x.com/2021/08/17/f4nRnU.png"></td>
    </tr>
</table>
