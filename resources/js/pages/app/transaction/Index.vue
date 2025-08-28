<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { handleDelete, handleFetchItems } from "@/helpers/client-req-handler";
import { getQueryParams } from "@/helpers/utils";
import { useQuasar } from "quasar";
import { createMonthOptions, createYearOptions } from "@/helpers/options";
import { formatDateTime, formatNumberWithSymbol } from "@/helpers/formatter";
import { usePageStorage } from "@/composables/usePageStorage";
import { usePartyFilter } from "@/composables/usePartyFilter";
import { useTransactionCategoryFilter } from "@/composables/useTransactionCategoryFilter";
import LongTextView from "@/components/LongTextView.vue";
import useTableHeight from "@/composables/useTableHeight";

const storage = usePageStorage("transaction");
const title = "Transaksi";
const page = usePage();
const tableRef = ref(null);
const filterToolbarRef = ref(null);
const tableHeight = useTableHeight(filterToolbarRef);
const $q = useQuasar();
const showFilter = ref(storage.get("show-filter", false));
const rows = ref([]);
const loading = ref(true);

const currentYear = new Date().getFullYear();
const currentMonth = new Date().getMonth() + 1;

const years = [
  { label: "Semua Tahun", value: "all" },
  { label: `${currentYear}`, value: currentYear },
  ...createYearOptions(currentYear - 2, currentYear - 1).reverse(),
];

const months = [
  { value: "all", label: "Semua Bulan" },
  ...createMonthOptions(),
];

const { filteredCategories, filterCategories } = useTransactionCategoryFilter(
  page.props.categories,
  true
);
const { filteredParties, filterParties } = usePartyFilter(
  page.props.parties,
  true
);

const types = [
  { value: "all", label: "Semua Jenis" },
  ...Object.entries(window.CONSTANTS.TRANSACTION_TYPES).map(
    ([value, label]) => ({
      value,
      label,
    })
  ),
];

const filter = reactive(
  storage.get("filter", {
    search: "",
    category_id: "all",
    party_id: "all",
    type: "all",
    year: currentYear,
    month: currentMonth,
    ...getQueryParams(),
  })
);

const pagination = ref(
  storage.get("pagination", {
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 10,
    sortBy: "id",
    descending: true,
  })
);

const columns = [
  {
    name: "datetime",
    label: "Waktu",
    field: "datetime",
    align: "left",
    sortable: true,
  },
  {
    name: "party",
    label: "Pihak",
    field: "party",
    align: "left",
  },
  {
    name: "type",
    label: "Jenis",
    field: "type",
    align: "left",
  },
  {
    name: "category",
    label: "Kategori",
    field: "category",
    align: "left",
  },
  {
    name: "amount",
    label: "Jumlah (Rp.)",
    field: "amount",
    align: "right",
  },
  {
    name: "notes",
    label: "Keterangan",
    field: "notes",
    align: "left",
  },
  {
    name: "action",
    align: "right",
  },
];

onMounted(() => {
  fetchItems();
});

const deleteItem = (row) =>
  handleDelete({
    message: `Hapus transaksi #-${row.id}?`,
    url: route("app.transaction.delete", row.id),
    fetchItemsCallback: fetchItems,
    loading,
  });

const fetchItems = (props = null) => {
  handleFetchItems({
    pagination,
    filter,
    props,
    rows,
    url: route("app.transaction.data"),
    loading,
    tableRef,
  });
};

const onFilterChange = () => {
  if (filter.year === "all") {
    filter.month = "all";
  }
  fetchItems();
};

const computedColumns = computed(() => {
  if ($q.screen.gt.sm) return columns;
  return columns.filter(
    (col) => col.name === "datetime" || col.name === "action"
  );
});

const onRowClicked = (row) =>
  router.get(route("app.transaction.detail", { id: row.id }));

watch(
  () => filter.year,
  (newVal) => {
    if (newVal === null) {
      filter.month = null;
    }
  }
);

watch(showFilter, () => storage.set("show-filter", showFilter.value), {
  deep: true,
});
watch(filter, () => storage.set("filter", filter), { deep: true });
watch(pagination, () => storage.set("pagination", pagination.value), {
  deep: true,
});
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <template #right-button>
      <q-btn
        icon="add"
        dense
        color="primary"
        @click="router.get(route('app.transaction.add'))"
      />
      <q-btn
        class="q-ml-sm"
        :icon="!showFilter ? 'filter_alt' : 'filter_alt_off'"
        color="grey"
        dense
        @click="showFilter = !showFilter"
      />
      <q-btn
        icon="file_export"
        dense
        class="q-ml-sm"
        color="grey"
        style=""
        @click.stop
      >
        <q-menu
          anchor="bottom right"
          self="top right"
          transition-show="scale"
          transition-hide="scale"
        >
          <q-list style="width: 200px">
            <q-item
              clickable
              v-ripple
              v-close-popup
              :href="route('app.transaction.export', { format: 'pdf' })"
            >
              <q-item-section avatar>
                <q-icon name="picture_as_pdf" color="red-9" />
              </q-item-section>
              <q-item-section>Export PDF</q-item-section>
            </q-item>
            <q-item
              clickable
              v-ripple
              v-close-popup
              :href="route('app.transaction.export', { format: 'excel' })"
            >
              <q-item-section avatar>
                <q-icon name="csv" color="green-9" />
              </q-item-section>
              <q-item-section>Export Excel</q-item-section>
            </q-item>
          </q-list>
        </q-menu>
      </q-btn>
    </template>
    <template #header v-if="showFilter">
      <q-toolbar class="filter-bar" ref="filterToolbarRef">
        <div class="row q-col-gutter-xs items-center q-pa-sm full-width">
          <q-select
            v-model="filter.year"
            :options="years"
            label="Tahun"
            dense
            outlined
            class="custom-select col-xs-12 col-md-4 col-sm-6"
            emit-value
            map-options
            @update:model-value="onFilterChange"
          />
          <q-select
            v-model="filter.month"
            :options="months"
            label="Bulan"
            dense
            outlined
            class="custom-select col-xs-12 col-md-4 col-sm-6"
            emit-value
            map-options
            :disable="filter.year === null || filter.year === 'all'"
            @update:model-value="onFilterChange"
          />
          <q-select
            dense
            outlined
            class="custom-select col-xs-12 col-md-4 col-sm-6"
            v-model="filter.party_id"
            label="Pihak"
            :options="filteredParties"
            @filter="filterParties"
            use-input
            input-debounce="300"
            map-options
            emit-value
            clearable
            @update:model-value="onFilterChange"
          >
          </q-select>
          <q-select
            dense
            outlined
            class="custom-select col-xs-12 col-md-4 col-sm-6"
            v-model="filter.category_id"
            label="Kategori"
            :options="filteredCategories"
            @filter="filterCategories"
            use-input
            input-debounce="300"
            map-options
            emit-value
            clearable
            @update:model-value="onFilterChange"
          >
          </q-select>

          <q-select
            v-model="filter.type"
            :options="types"
            label="Jenis Transaksi"
            dense
            class="custom-select col-xs-12 col-md-4 col-sm-6"
            map-options
            emit-value
            outlined
            @update:model-value="onFilterChange"
          />
          <q-input
            class="col"
            outlined
            dense
            debounce="300"
            v-model="filter.search"
            placeholder="Cari"
            clearable
          >
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </q-toolbar>
    </template>
    <div class="q-pa-sm">
      <q-table
        ref="tableRef"
        flat
        bordered
        square
        color="primary"
        row-key="id"
        virtual-scroll
        v-model:pagination="pagination"
        :filter="filter.search"
        :loading="loading"
        :columns="computedColumns"
        :rows="rows"
        :rows-per-page-options="[10, 25, 50]"
        :style="{ height: tableHeight }"
        @request="fetchItems"
        binary-state-sort
        class="full-height-table"
      >
        <template v-slot:loading>
          <q-inner-loading showing color="red" />
        </template>
        <template v-slot:no-data="{ icon, message, filter }">
          <div class="full-width row flex-center text-grey-8 q-gutter-sm">
            <span>
              {{ message }}
              {{ filter ? " with term " + filter : "" }}</span
            >
          </div>
        </template>
        <template v-slot:body="props">
          <q-tr
            :props="props"
            @click="onRowClicked(props.row)"
            class="cursor-pointer"
          >
            <q-td key="datetime" :props="props" class="wrap-column">
              <div>
                <q-icon v-if="!$q.screen.gt.sm" name="calendar_today" />
                {{ formatDateTime(props.row.datetime) }}
                <template v-if="props.row.image_path">
                  <q-icon name="image" color="green" />
                </template>
              </div>
              <template v-if="!$q.screen.gt.sm">
                <div v-if="props.row.party">
                  <q-icon name="person" /> {{ props.row.party.name }}
                </div>
                <div v-if="props.row.category">
                  <q-icon name="category" /> {{ props.row.category.name }}
                </div>
                <div>
                  <q-icon name="category" />
                  {{ $CONSTANTS.TRANSACTION_TYPES[props.row.type] }}
                </div>
                <div>
                  <q-icon name="money" />
                  <span
                    :class="props.row.amount >= 0 ? 'text-green' : 'text-red'"
                  >
                    Rp.
                    {{ formatNumberWithSymbol(props.row.amount) }}
                  </span>
                </div>
                <div v-if="props.row.notes">
                  <long-text-view
                    v-if="props.row.notes"
                    :text="props.row.notes"
                    :max-length="50"
                  />
                </div>
              </template>
            </q-td>
            <q-td key="party" :props="props">
              {{ props.row.party?.name }}
            </q-td>
            <q-td key="type" :props="props">
              {{ $CONSTANTS.TRANSACTION_TYPES[props.row.type] }}
            </q-td>
            <q-td key="category" :props="props">
              {{ props.row.category?.name }}
            </q-td>
            <q-td
              key="amount"
              :props="props"
              style="text-align: right"
              :class="props.row.amount >= 0 ? 'text-green' : 'text-red'"
            >
              {{ formatNumberWithSymbol(props.row.amount) }}
            </q-td>
            <q-td key="notes" :props="props">
              <long-text-view
                v-if="props.row.notes"
                :text="props.row.notes"
                :max-length="50"
              />
            </q-td>
            <q-td key="action" :props="props">
              <div class="flex justify-end">
                <q-btn
                  icon="more_vert"
                  dense
                  flat
                  style="height: 40px; width: 30px"
                  @click.stop
                >
                  <q-menu
                    anchor="bottom right"
                    self="top right"
                    transition-show="scale"
                    transition-hide="scale"
                  >
                    <q-item
                      clickable
                      v-ripple
                      v-close-popup
                      @click.stop="
                        router.get(
                          route('app.transaction.duplicate', props.row.id)
                        )
                      "
                    >
                      <q-item-section avatar>
                        <q-icon name="file_copy" />
                      </q-item-section>
                      <q-item-section> Duplikat </q-item-section>
                    </q-item>
                    <q-item
                      clickable
                      v-ripple
                      v-close-popup
                      @click.stop="
                        router.get(route('app.transaction.edit', props.row.id))
                      "
                    >
                      <q-item-section avatar>
                        <q-icon name="edit" />
                      </q-item-section>
                      <q-item-section>Edit</q-item-section>
                    </q-item>
                    <q-list style="width: 200px">
                      <q-item
                        @click.stop="deleteItem(props.row)"
                        clickable
                        v-ripple
                        v-close-popup
                      >
                        <q-item-section avatar>
                          <q-icon name="delete_forever" />
                        </q-item-section>
                        <q-item-section>Hapus</q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </q-btn>
              </div>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </authenticated-layout>
</template>
