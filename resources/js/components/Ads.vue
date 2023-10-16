<script setup>
  import { onMounted, ref, toRaw } from 'vue';
  import useUtility from '@/composables/useUtility.js';


  // === Composables ===
  const utility = useUtility(import.meta);


  // === Data ===
  const ads = ref([]);
  const selectedAd = ref();


  // === Methods ===
  const fetchAds = async () => {
    // console.log(`[${utility.currentFileName}::fetchAds()] Fetching ads...`);
    const apiResponse = await axios.get('/api/ads');

    console.log(`[${utility.currentFileName}::fetchAds()] apiResponse`, apiResponse);
    if (apiResponse.status !== 200) {
        return [];
    }

    if (apiResponse?.data?.error) {
      console.error(`[${utility.currentFileName}::fetchAds()] Error encountered retrieving ads`, apiResponse.data.error?.message);
      return [];
    }

    if (apiResponse?.data) {
      return apiResponse?.data;
    }

    return [];
  };


  // === Life Cycle Hooks ===
  onMounted(async () => {
    ads.value = (await fetchAds())
      .map((ad) => ({ title: ad.image_alt_text, value: ad.ad_code }));
    console.log(`[${utility.currentFileName}::onMounted()] ads.value`, toRaw(ads.value));
  });
</script>

<template>
    <VSelect
        bg-color="deep-orange-accent-3"
        density="comfortable"
        label="Ad Code"
        variant="outlined"
        v-model="selectedAd"
        :items="ads"
    >
    </VSelect>

    selectedAd: {{ selectedAd }}
</template>
