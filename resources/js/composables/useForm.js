/**
 * Form and validation related functions
 */
import { ref, toRaw } from 'vue';
import axios from 'axios';
import useProcessing from '@/composables/useProcessing.js';
import useUtility from '@/composables/useUtility.js';


// === Composables ===
const processing = useProcessing();
const utility = useUtility(import.meta);


/**
 *
 * @param {string} modelPathName Determines api path segment, e.g. 'ad', 'ad-types'
 * @param {Function} removeFromTableItems
 * @param {Object} vm Vue internal component instance from `getCurrentInstance()`
 * @returns
 */
export default (modelPathName) => {
  const formData = ref(null);
  const validationRules = ref({});
  // Intended to be initialized first time it is used, based on modelPathName
  const primaryKeyName = ref(null);

  /**
   * Get the primary key name of the model `modelPathName`
   */
  const getPrimaryKeyName = async () => {
    if (primaryKeyName.value) {
      return primaryKeyName.value;
    }

    primaryKeyName.value = (await axios.get(`/api/${modelPathName}/primary-key-name`))?.data;
    return primaryKeyName.value;
  }

  /**
   * Fetch validation rules defined in the Laravel model
   */
  const fetchValidationRules = async () => {
    const url = `/${modelPathName}/validation-rules`;
    const response = await axios.get(url);
    validationRules.value = response.data;
  };

  /**
   * Processing Save button click after validation has passed
   *
   * @param {Function} callback Additional function to run if submission is successful
   */
  const onSubmit = async (callback = null) => {
    // console.log(`[${utility.currentFileName}::onSubmit()] formData.value:`, toRaw(formData.value));

    const eventCode = processing.generateEventCode('SUBMIT_FORM');
    processing.setEvent(eventCode);

    try {
      const primaryKeyName = await getPrimaryKeyName();
      const primaryKeyValue = formData.value[primaryKeyName];
      // console.log(`[${utility.currentFileName}::onSubmit()] primaryKeyName, primaryKeyValue:`, primaryKeyName, primaryKeyValue);

      const actionPath = primaryKeyValue ? `update/${primaryKeyValue}` : 'store';
      const url = `/api/${modelPathName}/${actionPath}`;        // e.g. /api/ad/update/BikePeloton
      // console.log(`[${utility.currentFileName}::onSubmit()] url`, url);
      const response = await axios.post(url, { ...formData.value });

      if (response.data.status == 'success') {
        formData.value = response.data.result;

        if (callback) {
          await callback(formData.value[primaryKeyName], formData.value);
        }
      }

      // vm.proxy.$toast({
      //   component: ToastificationContent,
      //   props: {
      //     title: response.data.message,
      //     icon: response.data.status == 'success' ? 'CheckIcon' : 'XIcon',
      //     variant: response.data.status,
      //   },
      // });
    } catch (error) {
      if (error.response && error.response.status == 422) {
        // refFormObserver.value.setErrors(error.response.data.errors);

        // vm.proxy.$toast({
        //   component: ToastificationContent,
        //   props: {
        //     icon: 'XIcon',
        //     text: Object.keys(error.response.data.errors).join(', '),
        //     title: 'Data validation failed',
        //     variant: 'danger',
        //   },
        // });
      } else {
        console.log(`[${utility.currentFileName}::onSubmit()] Error encountered`, error);
      }
    } finally {
      processing.clearEvent(eventCode);
    }
  };

  /**
   * Reset formData to empty, allowing entry of a new ad
   */
  const reset = () => {
    formData.value = {};
  };

  /**
   * Perform an immediate update on a database field
   *
   * Note: Will reset `formData`
   *
   * @param {*} primaryKeyValue
   * @param {string} fieldName
   * @param {*} value
   */
  const updateField = async (primaryKeyValue, fieldName, fieldValue) => {
    // Clear existing data
    reset();

    const primaryKeyName = await getPrimaryKeyName();

    formData.value[primaryKeyName] = primaryKeyValue;
    formData.value[fieldName] = fieldValue;
    formData.value['skip_validation'] = true;

    onSubmit();
  };


  return {
    fetchValidationRules,
    formData,
    onSubmit,
    reset,
    updateField,
  };
};
