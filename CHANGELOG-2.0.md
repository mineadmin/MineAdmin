# v2.0 - TBD

## v2.0.0-beta.6

- [#134](https://github.com/mineadmin/MineAdmin/pull/134) Fix data migration fill failure due to file name not found class error
- [#116](https://github.com/mineadmin/MineAdmin/pull/116) Optimise online user statistics interface.
- [#111](https://github.com/mineadmin/MineAdmin/pull/111) Modify handleSearch conditional check function and adjust primary key to support snowflake ID and UUID
- [#205](https://github.com/mineadmin/MineAdmin/pull/205) Specify swagger component version
- [#213](https://github.com/mineadmin/MineAdmin/pull/213) Optimise `common/common.php` loading logic.
- [#215](https://github.com/mineadmin/MineAdmin/pull/215) Add generator migration file preview field
- [#217](https://github.com/mineadmin/MineAdmin/pull/217) Remove `redis->flushAll` to avoid misbehaviour when performing `mine:update` updates
- [#218](https://github.com/mineadmin/MineAdmin/pull/218) Fix some files generated with table prefixes when table prefix is not null.
- [#219](https://github.com/mineadmin/MineAdmin/pull/219) Add interface testing process under sql server environment, optimise existing unit test, change data structure of some migrated files.