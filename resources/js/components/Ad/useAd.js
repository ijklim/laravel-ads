import axios from 'axios';
import { reactive } from 'vue';
import useUtility from '@/composables/useUtility.js';


// === Composables ===
const utility = useUtility(import.meta);


// === Data ===
const state = reactive({
  adTypes: null,
});


// === Methods ===
const getAdTypes = async () => {
  if (state.adTypes && Array.isArray(state.adTypes)) {
    return state.adTypes;
  }

  const apiResponse = await axios.get('/api/ad-types');

  if (apiResponse.status !== 200) {
    state.adTypes = [];
  } else if (apiResponse?.data?.error) {
    state.adTypes = [];
  } else if (apiResponse?.data && Array.isArray(apiResponse.data) && 'ad_type' in apiResponse.data[0]) {
    state.adTypes = apiResponse.data
      .map(({ ad_type }) => ad_type);
  } else {
    state.adTypes = [];
  }

  // console.log(`[${utility.currentFileName}::getAdTypes()] state.adTypes`, state.adTypes);
  return state.adTypes;
};


export default () => {
  return {
    getAdTypes,
  }
};
