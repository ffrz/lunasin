<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { getQueryParams } from "@/helpers/utils";

// Import komponen yang sudah dipisah
import SummaryCards from "./cards/SummaryCards.vue";
import ChartCards from "./cards/ChartCards.vue";
import TopCards from "./cards/TopCards.vue";

const page = usePage();
// Dummy Data
const dashboardData = ref(page.props.data);

const title = "Dashboard";
const showFilter = ref(false);
const selectedMonth = ref(getQueryParams()["month"] ?? "this_month");
const monthOptions = ref([
  { value: "this_month", label: "Bulan Ini" },
  { value: "this_week", label: "Minggu Ini" },
  { value: "prev_week", label: "Minggu Lalu" },
  { value: "prev_month", label: "1 Bulan Sebelumnya" },
  { value: "prev_2month", label: "2 Bulan Sebelumnya" },
  { value: "prev_3month", label: "3 Bulan Sebelumnya" },
  // { value: "custom", label: "Custom" },
]);

const onFilterChange = () => {
  router.visit(route("app.dashboard", { month: selectedMonth.value }));
};
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <template #right-button>
      <q-btn
        class="q-ml-sm"
        :icon="!showFilter ? 'filter_alt' : 'filter_alt_off'"
        color="grey"
        dense
        @click="showFilter = !showFilter"
      />
    </template>
    <template #header v-if="showFilter">
      <q-toolbar class="filter-bar">
        <div class="row q-col-gutter-xs items-center q-pa-sm full-width">
          <q-select
            class="custom-select col-12"
            style="min-width: 150px"
            v-model="selectedMonth"
            :options="monthOptions"
            label="Bulan"
            dense
            map-options
            emit-value
            outlined
            @update:model-value="onFilterChange"
          />
        </div>
      </q-toolbar>
    </template>

    <div class="q-pa-sm">
      <div class="q-pa-none">
        <!-- Menggunakan komponen SummaryCards -->
        <SummaryCards :summary="dashboardData.summary" />
      </div>

      <!-- Menggunakan komponen ChartComponent -->
      <ChartCards
        :monthly-transactions="dashboardData.monthlyTransactions"
        :transaction-category-distribution="
          dashboardData.transactionCategoryDistribution
        "
      />

      <!-- Menggunakan komponen TopLists -->
      <TopCards
        :top-debtors="dashboardData.topDebtors"
        :top-creditors="dashboardData.topCreditors"
      />
    </div>
  </authenticated-layout>
</template>

<style scoped>
.text-h6 {
  font-size: 16px;
  font-weight: bold;
}
</style>
