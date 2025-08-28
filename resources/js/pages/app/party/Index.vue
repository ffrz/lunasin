<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { handleDelete, handleFetchItems } from "@/helpers/client-req-handler";
import { getQueryParams } from "@/helpers/utils";
import { usePageStorage } from "@/composables/usePageStorage";
import { createOptions } from "@/helpers/options";
import { formatNumberWithSymbol } from "@/helpers/formatter";
import LongTextView from "@/components/LongTextView.vue";
import { useQuasar } from "quasar";
import useTableHeight from "@/composables/useTableHeight";

const $q = useQuasar();
const storage = usePageStorage("party");
const tableRef = ref(null);
const filterToolbarRef = ref(null);
const tableHeight = useTableHeight(filterToolbarRef);
const title = "Pihak-pihak";
const showFilter = ref(storage.get("show-filter", false));
const rows = ref([]);
const loading = ref(true);

const filter = reactive(
  storage.get("filter", {
    status: "all",
    type: "all",
    ...getQueryParams(),
  })
);

const pagination = ref(
  storage.get("pagination", {
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 10,
    sortBy: "name",
    descending: false,
  })
);

const columns = [
  { name: "name", label: "Nama", field: "name", align: "left", sortable: true },
  {
    name: "phone",
    label: "Telepon",
    field: "phone",
    align: "left",
  },
  {
    name: "address",
    label: "Alamat",
    field: "address",
    align: "left",
  },
  {
    name: "balance",
    label: "Utang / Piutang (Rp)",
    field: "balance",
    align: "right",
    sortable: true,
  },
  {
    name: "notes",
    label: "Catatan",
    field: "notes",
    align: "left",
  },
  { name: "action", align: "right" },
];

const statuses = [
  { value: "all", label: "Semua" },
  { value: "active", label: "Aktif" },
  { value: "inactive", label: "Tidak Aktif" },
];

const types = [
  { value: "all", label: "Semua" },
  ...createOptions(window.CONSTANTS.PARTY_TYPES),
];

onMounted(() => {
  fetchItems();
});

const deleteItem = (row) =>
  handleDelete({
    message: `Hapus pihak ${row.name}?`,
    url: route("app.party.delete", row.id),
    fetchItemsCallback: fetchItems,
    loading,
    tableRef,
  });

const fetchItems = (props = null) => {
  handleFetchItems({
    pagination,
    filter,
    props,
    rows,
    url: route("app.party.data"),
    loading,
  });
};

const onFilterChange = () => fetchItems();
const onRowClicked = (row) =>
  router.get(route("app.party.detail", { id: row.id }));

const computedColumns = computed(() =>
  $q.screen.gt.sm
    ? columns
    : columns.filter((col) => ["name", "balance", "action"].includes(col.name))
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
        @click="router.get(route('app.party.add'))"
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
              :href="route('app.party.export', { format: 'pdf' })"
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
              :href="route('app.party.export', { format: 'excel' })"
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
            class="custom-select col-xs-12 col-md-4 col-sm-6"
            style="min-width: 150px"
            v-model="filter.status"
            :options="statuses"
            label="Status"
            dense
            map-options
            emit-value
            outlined
            @update:model-value="onFilterChange"
          />
          <q-select
            class="custom-select col-xs-12 col-md-4 col-sm-6"
            style="min-width: 150px"
            v-model="filter.type"
            :options="types"
            label="Jenis"
            dense
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
              {{ filter ? " with term " + filter : "" }}
            </span>
          </div>
        </template>

        <template v-slot:body="props">
          <q-tr
            :props="props"
            :class="!props.row.active ? 'bg-red-1' : ''"
            class="cursor-pointer"
            @click="onRowClicked(props.row)"
          >
            <q-td key="name" :props="props" class="wrap-column">
              <div>
                <q-icon
                  :name="props.row.type == 'company' ? 'domain' : 'person'"
                />
                {{ props.row.name }}
              </div>
              <template v-if="!$q.screen.gt.sm">
                <div v-if="props.row.phone">
                  <q-icon name="phone" /> {{ props.row.phone }}
                </div>
                <long-text-view
                  v-if="props.row.notes"
                  :text="props.row.notes"
                  :max-length="50"
                  icon="home_pin"
                />
                <long-text-view
                  v-if="props.row.address"
                  :text="props.row.address"
                  :max-length="50"
                />
              </template>
            </q-td>
            <q-td key="phone" :props="props">
              {{ props.row.phone }}
            </q-td>
            <q-td key="address" :props="props">
              <long-text-view :text="props.row.address" :max-length="100" />
            </q-td>
            <q-td
              key="balance"
              :props="props"
              :class="props.row.balance >= 0 ? 'text-green' : 'text-red'"
            >
              {{ formatNumberWithSymbol(props.row.balance) }}
            </q-td>
            <q-td key="notes" :props="props">
              <long-text-view :text="props.row.notes" :max-length="100" />
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
                    <q-list style="width: 200px">
                      <q-item
                        clickable
                        v-ripple
                        v-close-popup
                        @click.stop="
                          router.get(route('app.party.duplicate', props.row.id))
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
                          router.get(route('app.party.edit', props.row.id))
                        "
                      >
                        <q-item-section avatar>
                          <q-icon name="edit" />
                        </q-item-section>
                        <q-item-section>Edit</q-item-section>
                      </q-item>
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
