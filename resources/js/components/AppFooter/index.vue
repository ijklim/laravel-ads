<script setup>
  import { reactive } from 'vue';
  import { version as vuetifyVersion } from 'vuetify';

  // === State Management ===
  const state = reactive({
    packages: {
      Laravel: document.documentElement.dataset.versionLaravel,
      Vuetify: vuetifyVersion,
    },
  });
</script>

<template>
  <!-- Vuetify Flex: https://vuetifyjs.com/en/styles/flex -->
  <!-- (no longer necessary, here for ref only) flex-row-reverse: Components start from the right -->
  <!-- (no longer necessary, here for ref only) flex-1-0 + justify-space-between: Allows components to spread out to the edges -->
  <!-- Note: VAppBar gives better contral paired with VLayout compared to VFooter -->
  <!-- (no longer necessary, here for ref only) Note: VAppBar wraps element with another div, thus flex has to be redefined -->
  <VAppBar density="compact" location="bottom">
    <VContainer fluid>
      <VRow>
        <!-- d-md-block: Display only on breakpoint 'md' and above -->
        <VCol v-if="$vuetify.display.mdAndUp">
          <!-- Combine packages into comma separated string -->
          Built on:
          <span
            v-for="packageName in Object.keys(state.packages)"
            :key="packageName"
            class="package"
          >
            {{ packageName }} v.{{ state.packages[packageName] }}
          </span>
        </VCol>

        <VCol class="text-right">
          Proudly brought to you by <a href="https://ivan-lim.com" target="_blank">Ivan Lim</a>
        </VCol>
      </VRow>
    </VContainer>
  </VAppBar>
</template>

<style>
.package + .package {
  &::before {
    content: ', ';
  }
}
</style>