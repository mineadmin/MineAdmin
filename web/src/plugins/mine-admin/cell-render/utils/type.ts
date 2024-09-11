export type MakeOptions<T> = {
  [P in keyof T]?: T[P];
} & {
  prop?: string
}
