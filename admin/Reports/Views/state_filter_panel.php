<script type="text/x-template" id="my-form-template">
  <form>
    <div class="block">
      <div class="block-content block-content-full">
        <div class="row">
          <div class="col-md-2">
            <label>Year</label>
            <select class="form-control" v-model="year" required>
              <option v-for="year in years" :value="year.id">{{ year.name }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <label>Month</label>
            <select class="form-control" v-model="month">
              <option v-for="month in months" :value="month.id">{{ month.name }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <label>Fund Agency</label>
            <select class="form-control" v-model="fundAgencyId" @change="getDistricts">
              <option v-for="fundAgency in fundAgencies" :value="fundAgency.id">{{ fundAgency.name }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <label>District</label>
            <select class="form-control" v-model="districtId" @change="getBlocks">
              <option value="">Select District</option>
              <option v-for="district in districts" :value="district.id">{{ district.name }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <label>Block</label>
            <select class="form-control" v-model="blockId">
              <option value="">Select Block</option>
              <option v-for="block in blocks" :value="block.id">{{ block.name }}</option>
            </select>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-2">
            <button id="btn-filter" class="btn btn-outline btn-primary" @click.prevent="filter">Filter</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</script>

<div id="app">
  <my-form-template
    :years="years"
    :months="months"
    :fund-agencies="fundAgencies"
    :districts="districts"
    :blocks="blocks"
    v-model="formData"
  ></my-form-template>
</div>

<script>
const axios = require('axios');

new Vue({
  el: '#app',
  data() {
    return {
      years: [
        { id: 2021, name: '2021' },
        { id: 2022, name: '2022' },
        { id: 2023, name: '2023' },
      ],
      months: [
        { id: 1, name: 'January' },
        { id: 2, name: 'February' },
        { id: 3, name: 'March' },
      ],
      fundAgencies: [],
      districts: [],
      blocks: [],
      formData: {
        year: '',
        month: '',
        fundAgencyId: '',
        districtId: '',
        blockId: '',
      },
      get_district_url: '/get-districts',
      get_block_url: '/get-blocks',
    };
  },
  methods: {
    async getFundAgencies() {
      try {
        const response = await axios.get('/fund-agencies');
        this.fundAgencies = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    async getDistricts() {
      try {
        const response = await axios.get(`${this.get_district_url}`, {
          params: { fund_agency_id: this.formData.fundAgencyId },
        });
        this.districts = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    async getBlocks() {
      try {
        const response = await axios.get(`${this.get_block_url}`, {
          params: { district_id: this.formData.districtId, fund_agency_id: this.formData.fundAgencyId },
        });
        this.blocks = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    filter() {
      console.log(this.formData);
    },
  },
  created() {
    this.getFundAgencies();
  },
});
</script>
