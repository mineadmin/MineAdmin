import { useForm as useMaForm } from '@mineadmin/form'
import type { MaFormExpose } from '@mineadmin/form'

export default function useForm(refName: string): Promise<MaFormExpose> {
  return useMaForm(refName)
}
