<script setup>
  import { ref, toRaw, watch } from 'vue';
  import useAd from './useAd.js';
  import useForm from '@/composables/useForm.js';
  import useProcessing from '@/composables/useProcessing.js';
  import useUtility from '@/composables/useUtility.js';
  import { FORM_INPUT_RULES } from '@/utilities/formInputRules.js';


  // === Composables ===
  const ad = useAd();
  const form = useForm('ad');
  const processing = useProcessing();
  const utility = useUtility(import.meta);


  // === Data ===
  const adTypes = ref([]);

  // === Props ===
  const props = defineProps({
    adCode: {
      type: String,
      default: '',
    },
  })


  // === Methods ===
  const eventCodeFetchAd = processing.generateEventCode(`${utility.currentFileName}_fetchAd`);
  /**
   * Fetch ad with adCode from storage
   *
   * @param {string} adCode
   */
  const fetchAd = async (adCode) => {
    processing.setEvent(eventCodeFetchAd);

    // console.log(`[${utility.currentFileName}::fetchAds()] Fetching ads...`);
    const params = {
      params: {
        pk: adCode,
      },
    }
    const apiResponse = await axios.get('/api/ads', params);
    // console.log(`[${utility.currentFileName}::fetchAd()] apiResponse`, apiResponse);

    if (apiResponse.status !== 200) {
      processing.clearEvent(eventCodeFetchAd);
      return;
    }

    if (apiResponse?.data?.error) {
      console.error(`[${utility.currentFileName}::fetchAd()] Error encountered retrieving ads`, apiResponse.data.error?.message);

      processing.clearEvent(eventCodeFetchAd);
      return;
    }

    if (apiResponse?.data && apiResponse.data?.ad_code === adCode) {
      processing.clearEvent(eventCodeFetchAd);
      // adCode is the primary key, should only return 1 row
      return apiResponse.data;
    }

    processing.clearEvent(eventCodeFetchAd);
    return;
  };


  const eventCodeSubmitForm = processing.generateEventCode(`${utility.currentFileName}_submitForm`);
  /**
   * Submit form to save Ad information
   *
   * @param {*} event
   */
  const submitForm = async (event) => {
    processing.setEvent(eventCodeSubmitForm);

    const resultsFormValidation = await event;
    if (!resultsFormValidation.valid) {
      // Form fails validation
      return;
    }

    // Need await to avoid clearing event before form submission is completed
    await form.onSubmit();

    processing.clearEvent(eventCodeSubmitForm);
  };


  // === Watcher ===
  watch(() => props.adCode, async (newValue) => {
    if (!newValue) {
      return {}
    }

    form.formData.value = await fetchAd(props.adCode);
    adTypes.value = await ad.getAdTypes();

    // console.log(`[${utility.currentFileName}::watch::props.adCode] form.formData`, toRaw(form.formData.value));
    // console.log(`[${utility.currentFileName}::watch::props.adCode] adTypes.value`, toRaw(adTypes.value));
  }, { immediate: true });
</script>

<template>
  <!-- === Spinner === -->
  <div
    v-if="processing.isEventProcessing(eventCodeFetchAd)"
    class="text-center my-5"
  >
    <VProgressCircular color="amber" indeterminate :size="88" />
  </div>

  <!-- === Form that allows user to update Ad === -->
  <VForm
    v-else
    validate-on="submit lazy"
    @submit.prevent="submitForm"
  >
    <VRow v-if="form.formData.value">
      <!-- Panel: Action Buttons === -->
      <VCol cols="12" class="d-flex flex-row-reverse g-20">
        <!-- === Button: Submit Form === -->
        <VBtn
          color="success"
          density="default"
          type="submit"
          :disabled="processing.isEventProcessing()"
        >
          <span v-if="processing.isEventProcessing(eventCodeSubmitForm)">
            Saving... <VProgressCircular indeterminate :size="13" :width="2" />
          </span>

          <span v-else>
            Save
          </span>
        </VBtn>

        <!-- === Button: New === -->
        <VBtn
          color="info"
          density="default"
          type="button"
          @click="form.reset"
          :disabled="processing.isEventProcessing()"
        >
          New Ad
        </VBtn>
      </VCol>

      <!-- === Field: Ad Code === -->
      <VCol cols="12" md="3">
        <VTextField
          label="Ad Code"
          v-model="form.formData.value.ad_code"
          :rules="FORM_INPUT_RULES.NOT_EMPTY"
        />
      </VCol>

      <!-- === Field: Ad Type === -->
      <VCol cols="12" md="3" class="pt-0 pt-md-3">
        <VSelect
          label="Ad Type"
          v-model="form.formData.value.ad_type"
          :items="adTypes"
          :rules="FORM_INPUT_RULES.NOT_EMPTY"
        />
      </VCol>

      <!-- === Field: Price === -->
      <VCol cols="12" md="3" class="pt-0 pt-md-3">
        <VTextField
          label="Price"
          v-model="form.formData.value.price"
          :rules="FORM_INPUT_RULES.NOT_EMPTY"
        />
      </VCol>

      <!-- === Field: Price Discount Amount === -->
      <VCol cols="12" md="3" class="pt-0 pt-md-3">
        <VTextField
          label="Price Discount Amount"
          v-model="form.formData.value.price_discount_amount"
        />
      </VCol>

      <!-- === Field: Title === -->
      <VCol cols="12" md="6" class="pt-0">
        <VTextField
          label="Title"
          v-model="form.formData.value.title"
        />
      </VCol>

      <!-- === Field: Height === -->
      <VCol cols="12" md="2" class="pt-0">
        <VTextField
          label="Height (px)"
          type="number"
          v-model="form.formData.value.height"
        />
      </VCol>

      <!-- === Field: Width === -->
      <VCol cols="12" md="2" class="pt-0">
        <VTextField
          label="Width (px)"
          type="number"
          v-model="form.formData.value.width"
        />
      </VCol>

      <!-- === Field: Display Ratio === -->
      <VCol cols="12" md="2" class="pt-0">
        <VTextField
          label="Display Ratio"
          type="number"
          v-model="form.formData.value.display_ratio"
        />
      </VCol>

      <!-- === Image Preview === -->
      <!-- Hint: align-center (vertically middle), justify-center (horizontally center) -->
      <VCol cols="12">
        <figure class="text-center">
          <img :src="form.formData.value.url_segment_image" alt="Product Image Preview">
          <figcaption>
            Product Image Preview
          </figcaption>
        </figure>
      </VCol>



      <!-- === Field: Image Description === -->
      <VCol cols="12">
        <VTextField
          label="Image Description"
          v-model="form.formData.value.image_description"
        />
      </VCol>

      <!-- === Field: URL === -->
      <VCol cols="12" class="pt-0">
        <VTextField
          label="HRef"
          v-model="form.formData.value.href"
        />
      </VCol>

      <!-- === Field: Updated At === -->
      <VCol cols="6">
        <VTextField
          label="Updated At"
          v-model="form.formData.value.updated_at"
          :readonly="true"
        />
      </VCol>
    </VRow>
  </VForm>
</template>

<style scoped>
.g-20 {
  gap: 20px;
}
</style>