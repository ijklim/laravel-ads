<script setup>
  import Ads from '@/components/Ads.vue';
  import AppFooter from '@/components/AppFooter/index.vue';
  import AppHeader from '@/components/AppHeader/index.vue';
</script>

<template>
  <!-- Application Layout: https://vuetifyjs.com/en/features/application-layout/ -->
  <!-- Note: VLayout comes with better documentation than VApp, does not work too well with VFooter. -->
  <VLayout>
    <!-- === Header === -->
    <AppHeader />

    <!-- === Footer === -->
    <!-- Note: Affects the space Navigation Drawer takes if defined below -->
    <AppFooter :additionalPackages="['Vuetify 3']" />

    <!-- === Body === -->
    <!-- VMain necessary to start content below AppHeader, part of Vuetify Application Layout -->
    <VMain class="">
      <VContainer fluid>
        <VRow no-gutters class="mb-5 mb-md-0">
          <VCol
            cols="12"
          >
            <Ads />
          </VCol>
        </VRow>
      </VContainer>
    </VMain>
  </VLayout>
</template>

