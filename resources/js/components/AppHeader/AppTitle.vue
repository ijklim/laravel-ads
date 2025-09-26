<script setup>
  import { reactive } from 'vue';

  // === State Management ===
  const state = reactive({
    appName: document.documentElement.dataset.appName,
    versionApp: document.documentElement.dataset.versionApp,
  });
</script>

<template>
  <h1>
    {{ state.appName }}
    <small>v.{{ state.versionApp }}</small>
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
