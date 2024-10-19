<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<template>
  <div class="absolute left-0 z-3 h-full w-full">
    <svg class="mine-bg-svg">
      <defs>
        <radialGradient id=":S1:-desktop" cx="100%">
          <stop offset="0%" stop-color="rgba(56, 189, 248, 0.3)" />
          <stop offset="53.95%" stop-color="rgba(0, 71, 255, 0.09)" />
          <stop offset="100%" stop-color="rgba(10, 14, 23, 0)" />
        </radialGradient>
        <radialGradient id=":S1:-mobile" cy="100%">
          <stop offset="0%" stop-color="rgba(56, 189, 248, 0.3)" />
          <stop offset="53.95%" stop-color="rgba(0, 71, 255, 0.09)" />
          <stop offset="100%" stop-color="rgba(10, 14, 23, 0)" />
        </radialGradient>
      </defs>
      <rect width="100%" height="100%" fill="url(#:S1:-desktop)" />
      <rect width="100%" height="100%" fill="url(#:S1:-mobile)" />
    </svg>
  </div>
</template>
