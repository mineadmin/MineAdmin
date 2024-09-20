export default function getOnlyWorkAreaHeight(): number {
  return document.body.offsetWidth
    - (document.querySelector('.mine-bars')?.offsetHeight ?? 0)
    - (document.querySelector('.mine-header-main')?.offsetHeight ?? 0)
    - (document.querySelector('.mine-footer')?.offsetHeight ?? 0)
}
