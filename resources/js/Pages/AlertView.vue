<script setup>
import { computed, ref, onMounted } from "vue";
import {
  mdiMonitorCellphone,
  mdiTableBorder,
  mdiTableOff,
  mdiGithub,
} from "@mdi/js";
import SectionMain from "@/components/SectionMain.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import TableSampleClients from "@/components/TableSampleClients.vue";
import CardBox from "@/components/CardBox.vue";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseButton from "@/components/BaseButton.vue";
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue";
import { useAlertsStore } from "@/stores/alerts";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import 'primevue/resources/themes/saga-blue/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'
import { PrimeIcons, FilterMatchMode, FilterOperator } from 'primevue/api';

const alerts = ref();
const alertStore = useAlertsStore();

const lazyParams = ref({});
const dt = ref();
const loading = ref(true);
const totalRecords = ref(0);
const searchableCols = ref(['coin', 'condition', 'price', 'status']);
const filters = ref({
  'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
  'coin': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
  'condition': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
  'price': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
  'status': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
});

onMounted(async () => {
  lazyParams.value = {
    first: 0,
    rows: dt.value.rows,
    sortField: null,
    sortOrder: null,
    filters: filters.value
  };
  await loadLazyData();
});

const loadLazyData = async () => {
  loading.value = true;
  const params = {
    dt_params: JSON.stringify(lazyParams.value),
    searchable_columns: JSON.stringify(searchableCols.value),
  };
  await alertStore.fetch(params).then(response => {
    console.log('alerts', alertStore.data);
    alerts.value = alertStore.data.payload.data;
    totalRecords.value = alertStore.data.payload.total;
    loading.value = false;
  });
}


const onSort = (event) => {
  console.log('onSort');
  lazyParams.value = event;
  loadLazyData();
};

const onPage = (event) => {
  console.log('onPage');
  lazyParams.value = event;
  loadLazyData();
};

</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiTableBorder" title="Alerts" main>
      </SectionTitleLineWithButton>

      <CardBox class="mb-6" has-table>

        <div class="flex w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
          <div class="w-56 relative text-slate-500">
            <div class="p-inputgroup">
              <input v-model="filters['global'].value" type="text" class="form-control" @keyup.enter="onFilter($event)"
                placeholder="Search..." id="globalSearch" />
              <i class="pi pi-times pt-3 cursor-pointer " @click="resetSearchFilter" style='margin-left: -30px;' />
            </div>
          </div>
          <button class="ml-1 btn bg-custom-color-1000 text-white mr-1 mb-2" @click="onFilter($event)"
            id="globalSearchBtn">
            <SearchIcon class="w-5 h-5" />
          </button>
        </div>


        <DataTable :value="alerts" tableStyle="min-width: 50rem" :paginator="true" :lazy="true" :rows="10" ref="dt"
          dataKey="id" showGridlines stripedRows filterDisplay="menu" :globalFilterFields="searchableCols"
          responsiveLayout="scroll" v-model:filters="filters" :loading="loading" :totalRecords="totalRecords"
          @sort="onSort($event)" @page="onPage($event)">
          <template #empty>
            No Alerts found.
          </template>
          <template #loading>
            Loading Alerts data. Please wait.
          </template>
          <Column field="coin" header="Coin" :sortable="true"></Column>
          <Column field="condition" header="Condition" :sortable="true"></Column>
          <Column field="price" header="Price"></Column>
          <Column field="status" header="Status" :sortable="true"></Column>
        </DataTable>

      </CardBox>

    </SectionMain>
  </LayoutAuthenticated>
</template>
