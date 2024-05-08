# v2.0 - TBD

## v2.0.1-beta.7 TDB

- [#225](https://github.com/mineadmin/MineAdmin/pull/225) 优化 ws amqp 处理逻辑
- [#250](https://github.com/mineadmin/MineAdmin/pull/250) 优化 用户管理筛选逻辑
## v2.0.0-beta.6

- [#178](https://github.com/mineadmin/MineAdmin/pull/178) 代码生成器增加预览页面
- [#184](https://github.com/mineadmin/MineAdmin/pull/184) 移除全局 http 中间件
- [#205](https://github.com/mineadmin/MineAdmin/pull/205) 指定 swagger 组件版本
- [#213](https://github.com/mineadmin/MineAdmin/pull/213) 优化 `common/common.php` 加载逻辑
- [#215](https://github.com/mineadmin/MineAdmin/pull/215) 添加生成器迁移文件预览字段
- [#217](https://github.com/mineadmin/MineAdmin/pull/217) 删除 `redis->flushAll`，避免执行 `mine:update`更新时误操作
- [#218](https://github.com/mineadmin/MineAdmin/pull/218) 修复表前缀不为空时生成的某些文件自动带上了表前缀
- [#219](https://github.com/mineadmin/MineAdmin/pull/219) 增加 sql server 环境下的接口测试流程、优化现有单元测试，部分迁移文件数据结构变更

## v2.0.0-beta.5

- [#134](https://github.com/mineadmin/MineAdmin/pull/134) 修复因文件名未找到类错误而导致的数据迁移填充失败
- [#116](https://github.com/mineadmin/MineAdmin/pull/116) 优化在线用户统计界面。
- [#111](https://github.com/mineadmin/MineAdmin/pull/111) 修改 handleSearch 条件检查函数，并调整主键支持雪花 ID 和 UUID
