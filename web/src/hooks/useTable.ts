import { useTable as useMaTable } from '@mineadmin/table'
import type { MaTableExpose } from '@mineadmin/table'

export default function useTable(refName: string): Promise<MaTableExpose> {
  return useMaTable(refName)
}
