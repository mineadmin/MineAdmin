中文 | [English](./README-en.md) | [日本語](./README-ja.md)
# 项目介绍

<p align="center">
    <img src="web/public/logo.svg" width="120" alt="logo" />
</p>
<p align="center">
    <a href="https://www.mineadmin.com" target="_blank">官网</a> |
    <a href="https://doc.mineadmin.com" target="_blank">文档</a> | 
    <a href="https://demo.mineadmin.com" target="_blank">演示</a> |
    <a href="https://hyperf.wiki/3.0/#/" target="_blank">Hyperf官方文档</a> 
</p>

## 项目介绍

PHP有很多优秀的后台管理系统，但基于Swoole的后台管理系统没找到合适我自己的。
所以就开发了一套后台管理系统。系统可以用于网站管理后台、CMS、CRM、OA、ERP等。

后台系统基于 Hyperf 框架开发。企业级架构分层，轻松支撑创业公司及个人前期发展使用，使用少量的服务器资源媲美静态语言的性能。
前端使用Vue3 + Vite4 + Pinia + Arco，一端适配PC、移动端、平板

如果觉着还不错的话，就请点个 ⭐star 支持一下吧，这将是对我最大的支持和鼓励！
在使用 MineAdmin 前请认真阅读[《免责声明》](https://doc.mineadmin.com/guide/start/declaration.html)并同意该声明。

## 官方交流群
> QQ群用于交流学习，请勿水群

<a href="https://qm.qq.com/q/PJnEgr4D8C">
  <img src="https://svg.hamm.cn/badge.svg?key=QQ群&value=150105478" />
</a>

## 战略合作
[京策盾高防CDN - 抗DDOS/CC网络攻击的可靠服务商](https://www.jcdun.com/guoneigaofangcdn)

## 内置功能

1. 用户管理，完成用户添加、修改、删除配置，支持不同用户登录后台看到不同的首页
2. 角色管理，角色菜单权限分配、角色数据权限分配
3. 菜单管理，配置系统菜单和按钮等
4. 操作日志，用户对系统的一些正常操作的查询
5. 登录日志，用户登录系统的记录查询
6. 附件管理，管理当前系统上传的文件及图片等信息
7. 部门管理，可以管理组织架构
8. 岗位管理，在部门内管理，可以为部门设置岗位，再为用户分配岗位
9. 数据权限，数据权限功能跟随岗位而设置，同时，也可以对用户单独设置数据权限，使岗位的数据权限失效。
10. 应用市场，可下载各种基础应用、插件、前端组件等等

## 环境需求

- Swoole >= 5.0 并关闭 `Short Name`
- PHP >= 8.1 并开启以下扩展：
  - mbstring
  - json
  - pdo
  - openssl
  - redis
  - pcntl
- [x] Mysql >= 5.7
- [x] Pgsql >= 10
- [x] Sql Server Latest
- Sqlsrv is Latest
- Redis >= 4.0
- Git >= 2.x


## 下载项目
- MineAdmin没有使用SQL文件导入安装，系统使用Migrates迁移文件形式安装和填充数据，请知悉。

- 项目下载，请确保已经安装了 `Composer`
```shell
composer create-project mineadmin/mineadmin --keep-vcs
```

## 免责声明
[《免责声明》](https://doc.mineadmin.com/guide/start/declaration.html)

使用本软件不得用于开发违反国家有关政策的相关软件和应用，若因使用本软件造成的一切法律责任均与 `MineAdmin` 无关

## 体验地址

[体验地址](https://demo.mineadmin.com)
- 账号：admin
- 密码：123456

> 请勿添加脏数据

## 鸣谢

> 以下排名不分先后

[Hyperf 一款高性能企业级协程框架](https://hyperf.io/)

[Element Plus 基于 Vue 3，面向设计师和开发者的组件库](https://element-plus.org/)

[Swoole PHP协程框架](https://www.swoole.com)

[Vue](https://vuejs.org/)

[Vite](https://vitejs.cn/)

[Jetbrains 生产力工具](https://www.jetbrains.com/)

## 通过 OSCS 安全认证
[![OSCS Status](https://www.oscs1024.com/platform/badge/kanyxmo/MineAdmin.svg?size=large)](https://www.murphysec.com/dr/9ztZvuSN6OLFjCDGVo)

## star 趋势

[![Stargazers over time](https://starchart.cc/mineadmin/mineadmin.svg)](https://starchart.cc/mineadmin/mineadmin.svg)

## 贡献者

> 感谢所有参与 MineAdmin 开发的代码贡献者。 [[contributors](https://github.com/mineadmin/mineadmin/graphs/contributors)]
<a href="https://github.com/mineadmin/mineadmin/graphs/contributors">
<img src="https://contrib.rocks/image?repo=mineadmin/mineadmin" />
</a>

[![贡献者趋势](https://contributor-overtime-api.apiseven.com/contributors-svg?chart=contributorOverTime&repo=mineadmin/mineadmin)](https://www.apiseven.com/en/contributor-graph?chart=contributorOverTime&repo=mineadmin/mineadmin)

## 演示图片
[![pAdQKPJ.png](https://s21.ax1x.com/2024/10/22/pAdQKPJ.png)](https://imgse.com/i/pAdQKPJ)
[![pAdQlx1.png](https://s21.ax1x.com/2024/10/22/pAdQlx1.png)](https://imgse.com/i/pAdQlx1)
[![pAdQQ2R.png](https://s21.ax1x.com/2024/10/22/pAdQQ2R.png)](https://imgse.com/i/pAdQQ2R)
[![pAdQGqK.png](https://s21.ax1x.com/2024/10/22/pAdQGqK.png)](https://imgse.com/i/pAdQGqK)
[![pAdQYVO.png](https://s21.ax1x.com/2024/10/22/pAdQYVO.png)](https://imgse.com/i/pAdQYVO)
[![pAdQNIe.png](https://s21.ax1x.com/2024/10/22/pAdQNIe.png)](https://imgse.com/i/pAdQNIe)
[![pAdQaPH.png](https://s21.ax1x.com/2024/10/22/pAdQaPH.png)](https://imgse.com/i/pAdQaPH)
[![pAdQdGd.png](https://s21.ax1x.com/2024/10/22/pAdQdGd.png)](https://imgse.com/i/pAdQdGd)
