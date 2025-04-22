import WebStorageCache from 'web-storage-cache'

export type CacheType = 'localStorage' | 'sessionStorage'

export interface CacheOptions {
  /**
   * 超时时间，秒。
   * 默认无限大。
   */
  exp?: number

  /**
   * 为true时：当超过最大容量导致无法继续插入数据操作时，先清空缓存中已超时的内容后再尝试插入数据操作。
   * 默认为true。
   */
  force?: boolean
}

export default function useCache(type: CacheType = 'localStorage') {
  const prefix = import.meta.env.VITE_APP_STORAGE_PREFIX
  const cache = new WebStorageCache({ storage: type })

  /**
   * 往缓存中插入数据。
   * @param key
   * @param value 支持所有可以JSON.parse 的类型。注：当为undefined的时候会执行 remove(key)操作。
   * @param opts
   */
  const set = (key: string, value: any, opts: CacheOptions = {}): void => {
    cache.set(prefix.concat(key), value, opts)
  }

  /**
   * 根据key获取缓存中未超时数据。返回相应类型String、Boolean、PlainObject、Array的值。
   */
  const get = (key: string, defaultValue: any = null): any => {
    return cache.get(prefix.concat(key)) || defaultValue
  }

  /**
   * 根据key删除缓存中的值
   */
  const remove = (key: string): void => {
    cache.delete(prefix.concat(key))
  }

  /**
   * 删除缓存中所有通过WebStorageCache存储的超时值
   */
  const removeAllExpires = (): void => {
    cache.deleteAllExpires()
  }

  /**
   * 根据key为已存在的（未超时的）缓存值以当前时间为基准设置新的超时时间。
   * @param key
   * @param exp 单位：秒 js对象包含exp属性（以当前时间为起点的新的超时时间）
   */
  const touch = (key: string, exp: number): void => {
    cache.touch(prefix.concat(key), exp)
  }

  return {
    cache,
    prefix,
    set,
    get,
    remove,
    removeAllExpires,
    touch,
  }
}
