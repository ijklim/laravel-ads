<script setup>
  import { computed, onMounted, ref, toRaw } from 'vue';
  import { VDataTable } from 'vuetify/labs/VDataTable';
  import Ad from '@/components/Ad/index.vue';
  import useUtility from '@/composables/useUtility.js';


  // === Composables ===
  const utility = useUtility(import.meta);


  // === Data ===
  const ads = ref([]);
  const selectedAdCode = ref();
  const tableHeaders = [
    { title: 'Ad Code', key: 'ad_code', sortable: true },
    { title: 'Title', key: 'title', sortable: true },
    { title: 'Price', key: 'price', sortable: false },
    { title: 'Price Checked', key: 'html_updated_at', sortable: true },
  ];
  const tableItemsPerPage = ref(15);
  const tableSortBy = ref([
    {
      key: 'html_updated_at',
      order: 'desc',
    }
  ]);


  // === Computed ===
  const adsForDropdown = computed(() => {
    return [
      { title: '(Select an Ad)', value: '' },
      ...ads.value.map((ad) => ({
        title: ad.title ?? ad.ad_code,
        value: ad.ad_code
      })),
    ];
  });

  const adsForTable = computed(() => {
    // Hint: Must map to keys in `tableHeaders` above
    return ads.value
      .map((ad) => ({
        ad_code: ad.ad_code,
        html_updated_at: ad.html_updated_at,
        price: `$${ad.price} (${ad.price_discount_amount}) | ${ad.price_updated_at}`,
        title: ad.title,
        url_product: ad.url_product,
      }));
  });


  // === Methods ===
  /**
   * Fetch all ads from database
   *
   * @returns {Array.<string>}
   */
  const fetchAds = async () => {
    // console.log(`[${utility.currentFileName}::fetchAds()] Fetching ads...`);
    const apiResponse = await axios.get('/api/ads');

    // console.log(`[${utility.currentFileName}::fetchAds()] apiResponse`, apiResponse);
    if (apiResponse.status !== 200) {
        return [];
    }

    if (apiResponse?.data?.error) {
      console.error(`[${utility.currentFileName}::fetchAds()] Error encountered retrieving ads`, apiResponse.data.error?.message);
      return [];
    }

    if (apiResponse?.data) {
      return apiResponse?.data
        .sort((ad1, ad2) => (ad1.title > ad2.title));
    }

    return [];
  };


  // === Life Cycle Hooks ===
  onMounted(async () => {
    ads.value = (await fetchAds());
    // console.log(`[${utility.currentFileName}::onMounted()] ads.value`, toRaw(ads.value));
  });
</script>

<template>
    <!-- === Dropdown: Ad Selection === -->
    <VSelect
        bg-color="deep-orange-accent-3"
        label="Ad Code"
        variant="outlined"
        v-model="selectedAdCode"
        :clearable="true"
        :items="adsForDropdown"
    />

    <!-- === Ad Component that shows details of an Ad === -->
    <Ad
      :adCode="selectedAdCode"
      v-if="selectedAdCode"
    />

    <!-- === Lists of Ads === -->
    <!-- Doc: https://vuetifyjs.com/en/components/data-tables/basics/#usage -->
    <!-- Api: https://vuetifyjs.com/en/api/v-data-table/#links -->
    <VDataTable
      fixed-header
      height="calc(100vh - 250px)"
      v-model:items-per-page="tableItemsPerPage"
      :headers="tableHeaders"
      :items="adsForTable"
      :sort-by="tableSortBy"
      v-else
    >
      <template #item.title="{ item }">
        <!-- Link to product page -->
        <a
          rel="nofollow noopener noreferrer"
          target="_blank"
          v-if="item.url_product"
          :href="item.url_product"
        >
          {{ item.title }}
        </a>

        <span v-else>
          {{ item.title }}
        </span>
      </template>
    </VDataTable>
</template>
