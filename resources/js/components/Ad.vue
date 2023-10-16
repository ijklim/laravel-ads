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


  // === Computed ===
  watch(() => props.adCode, async (newValue) => {
    if (!newValue) {
      return {}
    }

    form.formData.value = await fetchAd(props.adCode);
    console.log(`[${utility.currentFileName}::watch::props.adCode] form.formData`, toRaw(form.formData.value));
  }, { immediate: true });


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

  const submitForm = async (event) => {
    const resultsFormValidation = await event;
    if (!resultsFormValidation.valid) {
      // Form fails validation
      return;
    }

    form.onSubmit();
  };
</script>

<template>
  <!-- === Form that allows user to update Ad === -->
  <VForm validate-on="submit lazy" @submit.prevent="submitForm">
    <VRow v-if="form.formData.value">
      <VCol md="6">
        <VTextField
          label="Ad Code"
          v-model="form.formData.value.ad_code"
          :rules="FORM_INPUT_RULES.NOT_EMPTY"
        />
      </VCol>

      <VCol md="6">
        <VTextField
          label="Ad Type"
          v-model="form.formData.value.ad_type"
          :rules="FORM_INPUT_RULES.NOT_EMPTY"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          label="URL"
          v-model="form.formData.value.href"
          :rules="FORM_INPUT_RULES.NOT_EMPTY"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          label="Image Alt Text"
          v-model="form.formData.value.image_alt_text"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          label="Image Description"
          v-model="form.formData.value.image_description"
        />
      </VCol>

      <VCol cols="6">
        <VTextField
          label="Updated At"
          v-model="form.formData.value.updated_at"
          :readonly="true"
        />
      </VCol>

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
