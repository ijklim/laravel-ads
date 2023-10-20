<script setup>
  import { toRaw, watch } from 'vue';
  import useForm from '@/composables/useForm.js';
  import useProcessing from '@/composables/useProcessing.js';
  import useUtility from '@/composables/useUtility.js';
  import { FORM_INPUT_RULES } from '@/utilities/formInputRules.js';


  // === Composables ===
  const form = useForm('ad');
  const processing = useProcessing();
  const utility = useUtility(import.meta);


  // === Props ===
  const props = defineProps({
    adCode: {
      type: String,
      default: '',
    },
  })


  // === Methods ===
  /**
   * Fetch ad with adCode from storage
   *
   * @param {string} adCode
   */
  const fetchAd = async (adCode) => {
    // console.log(`[${utility.currentFileName}::fetchAds()] Fetching ads...`);
    const params = {
      params: {
        adCode,
      },
    }
    const apiResponse = await axios.get('/api/ads', params);

    // console.log(`[${utility.currentFileName}::fetchAd()] apiResponse`, apiResponse);
    if (apiResponse.status !== 200) {
        return [];
    }

    if (apiResponse?.data?.error) {
      console.error(`[${utility.currentFileName}::fetchAd()] Error encountered retrieving ads`, apiResponse.data.error?.message);
      return [];
    }

    if (apiResponse?.data && Array.isArray(apiResponse?.data)) {
      // adCode is the primary key, should only return 1 row
      return apiResponse?.data[0];
    }

    return [];
  };

  /**
   * Submit form to save Ad information
   *
   * @param {*} event
   */
  const submitForm = async (event) => {
    const resultsFormValidation = await event;
    if (!resultsFormValidation.valid) {
      // Form fails validation
      return;
    }

    form.onSubmit();
  };

  /**
   * Update info from Amazon page
   *
   * todo: complete this method
   */
  const updateInfo = async () => {
    if (!form.formData.value?.href) {
      return false;
    }

    const response = await axios.get(form.formData.value.href);
    console.log(`[${utility.currentFileName}::updateInfo()] response`, response);
  };


  // === Watcher ===
  watch(() => props.adCode, async (newValue) => {
    if (!newValue) {
      return {}
    }

    form.formData.value = await fetchAd(props.adCode);
    // console.log(`[${utility.currentFileName}::watch::props.adCode] form.formData`, toRaw(form.formData.value));
  }, { immediate: true });
</script>

<template>
  <!-- === Form that allows user to update Ad === -->
  <VForm validate-on="submit lazy" @submit.prevent="submitForm">
    <VRow v-if="form.formData.value">
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
        <VTextField
          label="Ad Type"
          v-model="form.formData.value.ad_type"
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
      <VCol cols="12" class="pt-0">
        <VTextField
          label="Title"
          v-model="form.formData.value.title"
        />
      </VCol>

      <!-- === Image Preview === -->
      <!-- Hint: align-center (vertically middle), justify-center (horizontally center) -->
      <VCol cols="12" class="d-flex justify-center">
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
      <VCol cols="12" class="d-flex pt-0">
        <VTextField
          label="URL"
          v-model="form.formData.value.href"
          :rules="FORM_INPUT_RULES.NOT_EMPTY"
        />

        <!-- Button to retrive and update price related info from Amazon website -->
        <VBtn
          class="ml-5 mt-1"
          color="info"
          icon="mdi-update"
          type="button"
          @click="updateInfo"
          :disabled="processing.isEventProcessing()"
        >
        </VBtn>
      </VCol>

      <!-- === Field: Updated At === -->
      <VCol cols="6">
        <VTextField
          label="Updated At"
          v-model="form.formData.value.updated_at"
          :readonly="true"
        />
      </VCol>

      <!-- === Button: Submit Form === -->
      <VCol cols="12">
        <VBtn
          color="success"
          type="submit"
          :disabled="processing.isEventProcessing()"
        >
          Save
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
