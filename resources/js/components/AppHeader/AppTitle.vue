<script setup>
  import { appName, appVersion } from '@/utilities/constants.js';
</script>

<template>
  <h1>
    {{ appName }}
    <small>v.{{ appVersion }}</small>
  </h1>
</template>

<style scoped>
  h1 {
    /* Dynamically adjust font based on screen width */
    font-size: clamp(1rem, 4cqi, 1.4rem);
  }

  small {
    /* Font size base on parent's font size */
    font-size: 50%;
    font-style: italic;
  }
</style>
