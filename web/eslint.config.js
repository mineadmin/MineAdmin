import antfu from '@antfu/eslint-config'

export default antfu(
  {
    unocss: true,
    ignores: [
      'public',
      'dist*',
    ],
  },
  {
    rules: {
      'no-undefined': 'off',
      'vue/custom-event-name-casing': 'off',
      'vue/no-unused-refs': 'off',
      'perfectionist/sort-imports': 'off',
      'vue/component-definition-name-casing': 'off',
      'eslint-comments/no-unlimited-disable': 'off',
      'curly': ['error', 'all'],
      'antfu/consistent-list-newline': 'off',
      'ts/no-unused-expressions': 'off',
      'no-console': 'off',
      'vue/require-v-for-key': 'off',
      'array-callback-return': 'off',
    },
  },
  {
    files: [
      'src/**/*.vue',
      'src/**/*.tsx',
    ],
    rules: {
      'vue/block-order': ['error', {
        order: ['route', 'i18n', 'script', 'template', 'style'],
      }],
    },
  },
)
