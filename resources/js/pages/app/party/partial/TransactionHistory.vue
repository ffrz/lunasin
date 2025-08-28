<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { handleDelete, handleFetchItems } from "@/helpers/client-req-handler";
import { getQueryParams } from "@/helpers/utils";
import { useQuasar } from "quasar";
import { formatDateTime, formatNumberWithSymbol } from "@/helpers/formatter";
import LongTextView from "@/components/LongTextView.vue";

const $q = useQuasar();
const rows = ref([]);
const loading = ref(true);
const page = usePage();
const filter = reactive({
  party_id: page.props.data.id,
  ...getQueryParams(),
});

const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10,
  sortBy: "datetime",
  descending: true,
});

const columns = [
  {
    name: "datetime",
    label: "Waktu",
    field: "datetime",
    align: "left",
    sortable: true,
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

onMounted(() => fetchItems());

const deleteItem = (row) =>
  handleDelete({
    message: `Hapus interaksi ${row.name}?`,
    url: route("app.transaction.delete", row.id),
    fetchItemsCallback: fetchItems,
    loading,
  });

const fetchItems = (props = null) =>
  handleFetchItems({
    pagination,
    filter,
    props,
    rows,
    url: route("app.transaction.data"),
    loading,
  });

const onRowClicked = (row) =>
  router.get(route("app.transaction.detail", { id: row.id }));

const computedColumns = computed(() =>
  $q.screen.gt.sm
    ? columns
    : columns.filter((col) => ["datetime", "action"].includes(col.name))
);
</script>

<template>
  <div class="q-pa-none">
    <q-table
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
      @request="fetchItems"
      binary-state-sort
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
          @click="onRowClicked(props.row)"
          class="cursor-pointer"
        >
          <q-td key="datetime" :props="props" class="wrap-column">
            <div>
              <q-icon v-if="!$q.screen.gt.sm" name="calendar_today" />
              {{ formatDateTime(props.row.datetime) }}
            </div>
            <template v-if="!$q.screen.gt.sm">
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
              <long-text-view
                v-if="props.row.notes"
                :text="props.row.notes"
                :max-length="50"
                icon="notes"
              />
            </template>
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
            <long-text-view :text="props.row.notes" :max-length="10" />
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
</template>
