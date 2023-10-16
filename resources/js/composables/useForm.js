/**
 * Form and validation related functions
 */
import { ref } from 'vue';
import axios from 'axios';
import useProcessing from '@/composables/useProcessing.js';
import useUtility from '@/composables/useUtility.js';


// === Composables ===
const processing = useProcessing();
const utility = useUtility(import.meta);


/**
 *
 * @param {string} modelPathName e.g. 'page', 'review'
 * @param {Function} removeFromTableItems
 * @param {Object} vm Vue internal component instance from `getCurrentInstance()`
 * @returns
 */
export default (modelPathName) => {
  const formData = ref(null);
  const validationRules = ref({});

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
    const eventCode = processing.generateEventCode('SUBMIT_FORM');
    processing.setEvent(eventCode);

    try {
      const modelPrimaryKeyField = (await axios.get(`/api/${modelPathName}/primary-key-name`))?.data;
      const modelPrimaryKeyValue = formData.value[modelPrimaryKeyField];
      // console.log(`[${utility.currentFileName}::onSubmit()] modelPrimaryKeyField, modelPrimaryKeyValue:`, modelPrimaryKeyField, modelPrimaryKeyValue);

      const actionPath = modelPrimaryKeyValue ? `update/${modelPrimaryKeyValue}` : 'store';
      const url = `/api/${modelPathName}/${actionPath}`;        // e.g. /api/ad/update/BikePeloton
      // console.log(`[${utility.currentFileName}::onSubmit()] url`, url);
      const response = await axios.post(url, { ...formData.value });

      if (response.data.status == 'success') {
        formData.value = response.data.result;

        if (callback) {
          await callback(formData.value[modelPrimaryKeyField], formData.value);
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


  return {
    fetchValidationRules,
    formData,
    onSubmit,
  };
};
