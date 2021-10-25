<template>
  <div class="container">
    <div class="number">MineAdmin</div>
    <div class="contrast">
      <div class="circle"></div>
    </div>
    <div class="charging">
      <div>点击图标解锁</div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'HuaweiCharge',
  };
</script>

<style lang="scss" scoped>
  .container {
    position: absolute;
    bottom: 20vh;
    left: 50vw;
    width: 300px;
    height: 500px;
    transform: translateX(-50%);

    .number {
      position: absolute;
      top: 20%;
      z-index: 10;
      width: 300px;
      font-size: 28px;
      color: #fff;
      text-align: center;
    }

    .contrast {
      width: 300px;
      height: 300px;
      overflow: hidden;
      background-color: #000;
      filter: contrast(15) hue-rotate(0);
      animation: hueRotate 10s infinite linear;

      .circle {
        position: relative;
        width: 300px;
        height: 300px;
        filter: blur(8px);
        box-sizing: border-box;

        &::after {
          position: absolute;
          top: 40%;
          left: 50%;
          width: 200px;
          height: 200px;
          background-color: #00ff6f;
          border-radius: 42% 38% 62% 49% / 45%;
          content: '';
          transform: translate(-50%, -50%) rotate(0);
          animation: rotate 10s infinite linear;
        }

        &::before {
          position: absolute;
          top: 40%;
          left: 50%;
          z-index: 10;
          width: 176px;
          height: 176px;
          background-color: #000;
          border-radius: 50%;
          content: '';
          transform: translate(-50%, -50%);
        }
      }
    }

    .charging {
      font-size: 24px;
      text-align: center;
    }
  }

  @keyframes rotate {
    50% {
      border-radius: 45% / 42% 38% 58% 49%;
    }

    100% {
      transform: translate(-50%, -50%) rotate(720deg);
    }
  }

  @keyframes moveToTop {
    90% {
      opacity: 1;
    }

    100% {
      opacity: 0.1;
      transform: translate(-50%, -180px);
    }
  }

  @keyframes hueRotate {
    100% {
      filter: contrast(15) hue-rotate(360deg);
    }
  }
</style>
