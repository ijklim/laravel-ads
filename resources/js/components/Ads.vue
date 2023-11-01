<script setup>
  import { computed, onMounted, ref, toRaw } from 'vue';
  import { VDataTable } from 'vuetify/labs/VDataTable';
  import Ad from '@/components/Ad/index.vue';
  import useForm from '@/composables/useForm.js';
  import useUtility from '@/composables/useUtility.js';


  // === Composables ===
  const form = useForm('ad');
  const utility = useUtility(import.meta);


  // === Data ===
  const ads = ref([]);
  const selectedAdCode = ref();
  const tableHeaders = [
    { title: 'Ad Code', key: 'ad_code', sortable: true },
    { title: 'Title', key: 'title', sortable: true },
    { title: 'Enabled', key: 'is_enabled', sortable: true },
    { title: 'Price', key: 'price_info', sortable: false },
    { title: 'Price Updated', key: 'price_updated_at', sortable: true },
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
        is_enabled: ad.is_enabled,
        price_info: {
          price: ad.price,
          price_discount_amount: ad.price_discount_amount,
        },
        price_updated_at: ad.price_updated_at,
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

  /**
   * Transform mySQL timestamp into date string suitable for display purpose
   *
   * @param {string} mysqlTimestamp
   */
  const formatDate = (mysqlTimestamp) => {
    if (!mysqlTimestamp) {
      return '-';
    }

    const options = {
      day: 'numeric',       // numeric, 2-digit
      month: 'numeric',     // numeric, 2-digit, long, short, narrow
      year: '2-digit',      // numeric, 2-digit
      hour: '2-digit',      // numeric, 2-digit
      minute: '2-digit',    // numeric, 2-digit
    }

    return new Date(mysqlTimestamp).toLocaleDateString('en-US', options);
  };

  /**
   * Update database via api call based on field updated
   *
   * @param {Object} ad An item from array `adsForTable`
   * @param {string} fieldName e.g. is_enabled
   */
  const handleChangeAd = (ad, fieldName) => {
    // console.log(`[${utility.currentFileName}::handleChangeAd()] fieldName, ad.ad_code, ad[fieldName]`, fieldName, ad.ad_code, ad[fieldName]);
    form.updateField(ad.ad_code, fieldName, ad[fieldName]);
  }


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
      :items-per-page="50"
      :sort-by="tableSortBy"
      v-else
    >
      <!-- Keys available: index, item, internalItem, value, column -->

      <!-- === Field: html_updated_at === -->
      <template #item.html_updated_at="{ value }">
        {{ formatDate(value) }}
      </template>

      <!-- === Field: is_enabled === -->
      <template #item.is_enabled="{ item }">
        <VSwitch
          color="light-green"
          class="ml-3 switch"
          v-model="item.is_enabled"
          @change="handleChangeAd(item, 'is_enabled')"
        />
      </template>

      <!-- === Field: price_info === -->
      <template #item.price_info="{ value }">
        {{ `$${value.price}` }}{{ value.price_discount_amount ? ` (${value.price_discount_amount})` : '' }}
      </template>

      <!-- === Field: price_updated_at === -->
      <template #item.price_updated_at="{ value }">
        {{ formatDate(value) }}
      </template>

      <!-- === Field: title, Link to product page === -->
      <template #item.title="{ item }">
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

<style scoped>
  /* Remove extra vertical spacing and center switch */
  .switch {
    grid-template-areas: none;
    grid-template-columns: none;
    justify-content: start;
  }
</style>