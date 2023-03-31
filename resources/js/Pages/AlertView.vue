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
import {
  mdiDelete,
  mdiMagnify,
  mdiCheckCircle,
} from "@mdi/js";

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
const showSuccessBar = ref(false);
const showErrorBar = ref(false);

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

const deleteAlert = async (id) => {
  console.log('deleteAlert', id);
  await alertStore.delete(id).then(response => {
    console.log('delete response', response);
    if (response.status === 'success') {
      showSuccessBar.value = true;
    } else {
      showErrorBar.value = true;
    }
  });
  await loadLazyData();

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

const onFilter = (event) => {
  console.log('onFilter');
  lazyParams.value = event;
  loadLazyData();
};


const buttonSettingsModel = ref([]);

const buttonsOutline = computed(
  () => buttonSettingsModel.value.indexOf("outline") > -1
);

const buttonsSmall = computed(
  () => buttonSettingsModel.value.indexOf("small") > -1
);

const buttonsDisabled = computed(
  () => buttonSettingsModel.value.indexOf("disabled") > -1
);

const buttonsRounded = computed(
  () => buttonSettingsModel.value.indexOf("rounded") > -1
);

const notificationSettingsModel = ref([]);

const notificationsOutline = computed(
  () => notificationSettingsModel.value.indexOf("outline") > -1
);

</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiTableBorder" title="Alerts" main>
      </SectionTitleLineWithButton>


      <NotificationBar v-if="showSuccessBar" color="success" :icon="mdiCheckCircle" :outline="notificationsOutline">
        <b>Success state</b>. NotificationBar
        <template #right>
          <BaseButton label="x" :color="notificationsOutline ? 'success' : 'white'" :outline="notificationsOutline"
            rounded-full small />
        </template>
      </NotificationBar>

      <NotificationBar v-if="showErrorBar" color="danger" :icon="mdiAlertCircle" :outline="notificationsOutline">
        <b>Danger state</b>. NotificationBar
      </NotificationBar>

      <CardBox class="mb-6" has-table>

        <div class="flex w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
          <div class="w-56 relative text-slate-500">
            <div class="p-inputgroup">
              <input v-model="filters['global'].value" type="text" class="form-control" @keyup.enter="onFilter($event)"
                placeholder="Search..." id="globalSearch" />
              <i class="pi pi-times pt-3 cursor-pointer " @click="resetSearchFilter" style='margin-left: -30px;' />
            </div>
          </div>

          <BaseButton color="primary" :icon="mdiMagnify" :small="buttonsSmall" :outline="buttonsOutline"
            :disabled="buttonsDisabled" :rounded-full="buttonsRounded" @click="onFilter($event)" id="globalSearchBtn" />
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
          <Column field="id" header="ID" :sortable="true"></Column>
          <Column field="author" header="Author" :sortable="true"></Column>
          <Column field="coin" header="Coin" :sortable="true"></Column>
          <Column field="condition" header="Type" :sortable="true"></Column>
          <Column field="price" header="Price"></Column>
          <Column field="status" header="Status"></Column>
          <Column field="notes" header="Notes" :sortable="true"></Column>
          <Column :exportable="false" header="Actions">
            <template #body="{ data: data }">
              <div class="content-center flex justify-center">
                <p>
                  <BaseButton color="danger" :icon="mdiDelete" :small="buttonsSmall" :outline="buttonsOutline"
                    :disabled="buttonsDisabled" :rounded-full="buttonsRounded" @click="deleteAlert(data.id)" />
                </p>
              </div>
            </template>
          </Column>
        </DataTable>

      </CardBox>

    </SectionMain>
  </LayoutAuthenticated>
</template>
