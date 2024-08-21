/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import fs from 'node:fs'
import dayjs from 'dayjs'
import archiver from 'archiver'
import type { Plugin } from 'vite'

function sleep(ms: number) {
  return new Promise(resolve => setTimeout(resolve, ms))
}

export default function createArchiver(env: any): Plugin {
  const { VITE_BUILD_ARCHIVE } = env
  let outDir: string
  return {
    name: 'vite-plugin-archiver',
    apply: 'build',
    configResolved(resolvedConfig) {
      outDir = resolvedConfig.build.outDir
    },
    async closeBundle() {
      if (['zip', 'tar'].includes(VITE_BUILD_ARCHIVE)) {
        await sleep(1000)
        const archive = archiver(VITE_BUILD_ARCHIVE, {
          ...(VITE_BUILD_ARCHIVE === 'zip' && { zlib: { level: 9 } }),
          ...(VITE_BUILD_ARCHIVE === 'tar' && { gzip: true, gzipOptions: { level: 9 } }),
        })
        const output = fs.createWriteStream(`${outDir}.${dayjs().format('YYYY-MM-DD-HH-mm-ss')}.${VITE_BUILD_ARCHIVE === 'zip' ? 'zip' : 'tar.gz'}`)
        archive.pipe(output)
        archive.directory(outDir, false)
        await archive.finalize()
      }
    },
  }
}
