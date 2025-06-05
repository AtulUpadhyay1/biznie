<style>
  .loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.28);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    pointer-events: all;
  }

  .loader-container {
    position: relative;
    width: 100px;
    height: 100px;
  }

  /* Rings base style */
  .ring {
    position: absolute;
    top: 50%;
    left: 50%;
    border-radius: 50%;
    border-style: solid;
    border-color: transparent;
    border-top-color: #8A0707;
    border-bottom-color: #E01111;
    box-shadow: 0 0 12px rgba(234, 17, 17, 0.6);
    transform-origin: center;
    transform: translate(-50%, -50%);
  }

  /* Outer ring */
  .ring.outer {
    width: 100px;
    height: 100px;
    border-width: 5px;
    animation: spinClockwise 4.5s linear infinite;
  }

  /* Middle ring */
  .ring.middle {
    width: 72px;
    height: 72px;
    border-width: 6px;
    border-top-color: #B30A0A;
    border-bottom-color: #C71010;
    box-shadow: 0 0 10px rgba(179, 10, 10, 0.7);
    animation: spinCounterClockwise 3.2s linear infinite;
  }

  /* Inner ring */
  .ring.inner {
    width: 45px;
    height: 45px;
    border-width: 7px;
    border-top-color: #8A0707;
    border-bottom-color: #AA0B0B;
    box-shadow: 0 0 8px rgba(138, 7, 7, 0.8);
    animation: spinClockwise 2.1s linear infinite;
  }

  /* Center dot */
  .center-dot {
    position: absolute;
    top: 50%; left: 50%;
    width: 16px;
    height: 16px;
    background-color: #8A0707;
    border-radius: 50%;
    box-shadow:
      0 0 10px #8A0707,
      0 0 20px #E01111;
    transform: translate(-50%, -50%);
    z-index: 10;
  }

  /* Ripple effect ring */
  .ripple-ring {
    position: absolute;
    top: 50%; left: 50%;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2.5px solid #E01111;
    transform: translate(-50%, -50%) scale(1);
    opacity: 0.7;
    animation: ripplePulse 2.5s ease-out infinite;
    z-index: 9;
  }

  .ripple-ring:nth-child(2) {
    animation-delay: 1.3s;
  }

  @keyframes spinClockwise {
    100% {
      transform: translate(-50%, -50%) rotate(360deg);
    }
  }

  @keyframes spinCounterClockwise {
    100% {
      transform: translate(-50%, -50%) rotate(-360deg);
    }
  }

  @keyframes ripplePulse {
    0% {
      transform: translate(-50%, -50%) scale(1);
      opacity: 0.7;
    }
    80% {
      opacity: 0;
      transform: translate(-50%, -50%) scale(3.5);
    }
    100% {
      opacity: 0;
      transform: translate(-50%, -50%) scale(3.5);
    }
  }
</style>

<div wire:loading>
  <div class="loading-overlay" aria-label="Loading animation" role="status" aria-live="polite">
    <div class="loader-container" aria-hidden="true">
      <div class="ring outer"></div>
      <div class="ring middle"></div>
      <div class="ring inner"></div>
      <div class="center-dot"></div>
      <div class="ripple-ring"></div>
      <div class="ripple-ring"></div>
    </div>
  </div>
</div>
