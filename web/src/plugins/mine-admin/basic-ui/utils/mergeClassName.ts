export default function mergeClassName(classList: string[], classMap: string | string[] | object): string {
  if (classMap !== null) {
    if (typeof classMap === 'string') {
      return [...classList, classMap].join(' ')
    }
    else {
      return [...classList, ...classMap as string[]].join(' ')
    }
  }
  else {
    return classList.join(' ')
  }
}
