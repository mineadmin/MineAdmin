export default function getOnlyWorkAreaHeight(): number {
  return document.body.offsetHeight
    - ((document.querySelector('.mine-bars') as HTMLElement)?.offsetHeight ?? 0)
    - ((document.querySelector('.mine-header-main') as HTMLElement)?.offsetHeight ?? 0)
    - ((document.querySelector('.mine-footer') as HTMLElement)?.offsetHeight ?? 0)
    - 48
}
