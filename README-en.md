[中文](./README.md) | English
# Projects

<p align="center">
    <img src="https://doc.mineadmin.com/logo.svg" width="120" />
</p>
<p align="center">
    <a href="https://www.mineadmin.com" target="_blank">official website</a> |
    <a href="https://doc.mineadmin.com" target="_blank">Document</a> | 
    <a href="https://demo.mineadmin.com" target="_blank">Demo</a> |
    <a href="https://hyperf.wiki/3.0/#/" target="_blank">Hyperf Official Document</a> 
</p>

<p align="center">
    <img src="https://gitee.com/xmo/MineAdmin/badge/star.svg?theme=dark" />
    <img src="https://gitee.com/xmo/MineAdmin/badge/fork.svg?theme=dark" />
    <img src="https://svg.hamm.cn/badge.svg?key=License&value=Apache-2.0&color=da4a00" />
    <img src="https://svg.hamm.cn/badge.svg?key=MineAdmin&value=v2.0 LTS" />
</p>

## Projects Description

PHP has a lot of good backend management system, but Swoole based backend management system did not find suitable for my own.
So it developed a set of background management system. The system can be used for website management background , CMS, CRM, OA, ERP and so on.

Background system based on Hyperf framework development. Enterprise-level architecture layered to easily support startups and personal pre-development use , using a small amount of server resources comparable to the performance of static language .
Front-end use of Vue3 + Vite4 + Pinia + Arco, one end of the adaptation of PC, mobile, tablet!

If you think it's not bad, please point a ⭐star support it, it will be the biggest support and encouragement to me!
Please read [Disclaimer](https://doc.mineadmin.com/guide/start/declaration.html) and agree to it before using MineAdmin.

- Tencent Cloud Special Offer: [Click to enter](http://txy.mineadmin.com)
- AliCloud Special Offer：[Click to enter](http://aly.mineadmin.com)

## Front-end repository address
Move to the front-end repository

- [Github MineAdmin-Vue](https://github.com/mineadmin/MineAdmin-Vue)
- [Gitee MineAdmin-Vue](https://gitee.com/mineadmin/MineAdmin-vue)

## Official communication group
> QQ group for communication and learning, please do not water group.

<img src="https://svg.hamm.cn/badge.svg?key=QQ群&value=150105478" />

## Built-in functions

1. user management, complete the user add, modify, delete configuration, support for different users to log in the background to see different home page
2. department management, departmental organization (company, department, group), tree structure show support for data permissions
3. position management, you can configure the user's position
4. role management, role menu rights allocation, role data rights allocation
5. menu management, configure system menus and buttons, etc.
6. dictionary management, the system often used and fixed data can be reused and maintained
7. system configuration, some common settings of the system management
8. operation log, the query of some normal operations of the system by users.
9. login log, user login system record query
10. online users, view the current logged-in users
11. service monitoring, view the current server status and PHP environment and other information
12. attachment management, management of the current system to upload files and pictures and other information
13. data table maintenance, the system's data table can be cleaned up debris and optimization
14. module management, manage all modules of the system
15. timed tasks, online (add, modify, delete) task scheduling including execution results logs
16. code generation, front and back-end code generation (php, vue, js, sql), support for downloading and generating to the module.
17. cache monitoring , view the Redis information and the system used key information
18. API management , application and interface management , interface authorization and other functions. Interface documents are automatically generated , input and output parameter checking , etc.
19. queue management, message queue management functions, message management, message sending. The use of ws mode instant messaging reminders (need to install rabbitMQ)
20. application market , you can download a variety of basic applications , plug-ins , front-end components , etc. (under development ...)

## Environment requirements

- Swoole >= 5.0 with `Short Name` turned off.
- PHP >= 8.1 and turn on the following extensions:
    - mbstring
    - json
    - pdo
    - openssl
    - redis
    - pcntl
- Mysql >= 5.7
- Redis >= 4.0
- Git >= 2.x


## Download the project
- Please note that MineAdmin does not use SQL file import for installation, the system uses Migrates migration files to install and populate the data.

- To download the project, make sure you have installed ``Composer``.
```shell
git clone https://gitee.com/xmo/MineAdmin && cd MineAdmin
composer config -g repo.packagist composer https://mirrors.tencent.com/composer/
composer install
```

## Project Installation

Open a terminal, execute the install command, and follow the prompts to configure the `.env` file step by step.
```shell
php bin/hyperf.php mine:install
```

When prompted with the following message
```shell
Reset the ".env" file. Please restart the service before running 
the installation command to continue the installation.
```

Execute the installation command again to execute the Migrates data migration file and SQL data fill to complete the installation.
```shell
php bin/hyperf.php mine:install
```

[Click here -> for FAQ](https://doc.mineadmin.com/faqs/)

## Disclaimer
[Disclaimer](https://doc.mineadmin.com/guide/start/declaration.html)

The use of this software shall not be used to develop any software or application that violates the relevant national policies, and `MineAdmin` has nothing to do with any legal responsibility caused by the use of this software.

## Trial Addresses

[Experience Address](https://demo.mineadmin.com)
- Account: superAdmin
- Password: admin123

> Please do not add dirty data

## Acknowledgments

> The following are in no particular order

[Hyperf a high-performance enterprise-class concurrent programming framework](https://hyperf.io/)

[Arco An enterprise-class design system from ByteHop](https://arco.design/)

[Swoole PHP Concurrent Programming Framework](https://www.swoole.com)

[Vue](https://vuejs.org/)

[Vite](https://vitejs.cn/)

[Jetbrains Productivity Tools](https://www.jetbrains.com/)

## OSCS Security Certified
![OSCS Status](https://www.oscs1024.com/platform/badge/kanyxmo/MineAdmin.svg?size=large)

## Stargazers over time

[![Stargazers over time](https://starchart.cc/mineadmin/mineadmin.svg)](https://starchart.cc/mineadmin/mineadmin.svg)


## Demo image
<img src="https://s1.ax1x.com/2022/07/31/vklKzR.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vkl8eK.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vkl1L6.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vklNJH.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vklJoD.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vkllsx.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vklZoF.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vklUWd.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vkl0yt.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vkltFe.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vkluW9.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vklnJJ.jpg" />
<img src="https://s1.ax1x.com/2022/07/31/vklmi4.jpg" />