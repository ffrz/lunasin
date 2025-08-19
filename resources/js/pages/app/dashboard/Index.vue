<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { getQueryParams } from "@/helpers/utils";
import { usePageStorage } from "@/composables/usePageStorage";

// Import komponen
import SummaryCards from "./cards/SummaryCards.vue";
import ChartCards from "./cards/ChartCards.vue";
import TopCards from "./cards/TopCards.vue";

const page = usePage();
const dashboardData = ref(page.props.data);
const storage = usePageStorage("dashboard");

const title = "Dashboard";
const showFilter = ref(storage.get("show-filter", false));

const selectedYear = ref(getQueryParams()["year"] ?? "this_year");

const yearOptions = ref([
  { value: "this_year", label: "Tahun Ini" },
  { value: "prev_year", label: "Tahun Lalu" },
  { value: "prev_2_year", label: "2 Tahun Lalu" },
  { value: "prev_3_year", label: "3 Tahun Lalu" },
  { value: "prev_4_year", label: "4 Tahun Lalu" },
  { value: "prev_5_year", label: "5 Tahun Lalu" },
]);


const onFilterChange = () => {
  router.visit(route("app.dashboard", { year: selectedYear.value }), {
    preserveState: true,
    preserveScroll: true,
  });
};

watch(showFilter, () => storage.set("show-filter", showFilter.value));

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
            v-model="selectedYear"
            :options="yearOptions"
            label="Tahun"
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
      <div class="text-caption text-bold text-grey-8 q-mb-sm text-center">
        Statistik Aktual
      </div>
      <div class="q-pa-none">
        <!-- Menggunakan komponen SummaryCards -->
        <SummaryCards :summary="dashboardData.summary" />
      </div>
      <!-- Menggunakan komponen TopLists -->
      <TopCards
        :top-debtors="dashboardData.topDebtors"
        :top-creditors="dashboardData.topCreditors"
      />

      <div class="text-caption text-bold text-grey-8 q-mb-sm text-center">
        Statistik
        {{ yearOptions.find((o) => o.value === selectedYear)?.label }}
      </div>
      <!-- Menggunakan komponen ChartComponent -->
      <ChartCards
        :monthly-transactions="dashboardData.monthlyTransactions"
        :transaction-category-distribution="
          dashboardData.transactionCategoryDistribution
        "
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
