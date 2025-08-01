<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { handleFetchItems, handleDelete } from "@/helpers/client-req-handler";
import { create_options, getQueryParams } from "@/helpers/utils";
import i18n from "@/i18n";
import { useQuasar } from "quasar";
import { usePageStorage } from '@/helpers/usePageStorage'

const storage = usePageStorage('users')

const roles = [
  { value: "all", label: "Semua" },
  ...create_options(window.CONSTANTS.USER_ROLES),
];

const statuses = [
  { value: "all", label: "Semua" },
  { value: "active", label: "Aktif" },
  { value: "inactive", label: "Tidak Aktif" },
];

const page = usePage();
const $q = useQuasar();
const currentUser = page.props.auth.user;
const title = i18n.global.t("users");
const rows = ref([]);
const loading = ref(true);
const showFilter = ref(storage.get('show-filter', false));

const filter = reactive(storage.get('filter', {
  role: "all",
  status: "active",
  search: "",
  ...getQueryParams(),
}));

const pagination = ref(storage.get('pagination', {
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10,
  sortBy: "username",
  descending: false,
}));

const columns = [
  { name: "username", label: "Username", field: "username", align: "left", sortable: true },
  { name: "name", label: "Nama", field: "name", align: "left", sortable: true },
  { name: "role", label: "Role", field: "role", align: "center" },
  { name: "work_area", label: "Area Kerja", field: "work_area", align: "center", },
  { name: "parent", label: "Supervisor", field: "parent", align: "center" },
  { name: "action", align: "right" },
];

onMounted(() => fetchItems());

const onFilterChange = () => fetchItems();

const fetchItems = (props = null) =>
  handleFetchItems({
    pagination,
    props,
    rows,
    loading,
    filter,
    url: route("admin.user.data"),
  });

const deleteItem = (row) =>
  handleDelete({
    url: route("admin.user.delete", row.id),
    message: `Hapus pengguna ${row.username}?`,
    fetchItemsCallback: fetchItems,
    loading,
  });

const computedColumns = computed(() =>
  $q.screen.gt.sm ? columns : columns.filter((col) => ["username", "action"].includes(col.name))
);

const onRowClicked = (row) => router.get(route("admin.user.detail", row.id));

watch(filter, () => storage.set('filter', filter), { deep: true })
watch(showFilter, () => storage.set('show-filter', showFilter.value), { deep: true })
watch(pagination, () => storage.set('pagination', pagination.value), { deep: true })

</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <template #right-button>
      <q-btn icon="add" dense color="primary" @click="router.get(route('admin.user.add'))"
        :disable="$page.props.auth.user.role != $CONSTANTS.USER_ROLE_ADMIN" />
      <q-btn class="q-ml-sm" :icon="!showFilter ? 'filter_alt' : 'filter_alt_off'" color="grey" dense
        @click="showFilter = !showFilter" />
      <q-btn icon="file_export" dense class="q-ml-sm" color="grey" style="" @click.stop>
        <q-menu anchor="bottom right" self="top right" transition-show="scale" transition-hide="scale">
          <q-list style="width: 200px">
            <q-item clickable v-ripple v-close-popup :href="route('admin.user.export', { format: 'pdf' })">
              <q-item-section avatar>
                <q-icon name="picture_as_pdf" color="red-9" />
              </q-item-section>
              <q-item-section>Export PDF</q-item-section>
            </q-item>
            <q-item clickable v-ripple v-close-popup :href="route('admin.user.export', { format: 'excel' })">
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
      <q-toolbar class="filter-bar">
        <div class="row q-col-gutter-xs items-center q-pa-sm full-width">
          <q-select v-model="filter.role" class="custom-select col-xs-12 col-sm-2" :options="roles" label="Role" dense
            map-options emit-value outlined style="min-width: 150px" @update:model-value="onFilterChange" />
          <q-select v-model="filter.status" class="custom-select col-xs-12 col-sm-2" :options="statuses" label="Status"
            dense map-options emit-value outlined style="min-width: 150px" @update:model-value="onFilterChange" />
          <q-input class="col" outlined dense debounce="300" v-model="filter.search" placeholder="Cari" clearable>
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </q-toolbar>
    </template>
    <div class="q-pa-sm">
      <q-table flat bordered square color="primary" row-key="id" virtual-scroll v-model:pagination="pagination"
        :filter="filter.search" :loading="loading" :columns="computedColumns" :rows="rows"
        :rows-per-page-options="[10, 25, 50]" @request="fetchItems" binary-state-sort>
        <template v-slot:loading>
          <q-inner-loading showing color="red" />
        </template>

        <template v-slot:no-data="{ icon, message, term }">
          <div class="full-width row flex-center text-grey-8 q-gutter-sm">
            <span>{{ message }} {{ term ? " with term " + term : "" }}</span>
          </div>
        </template>

        <template v-slot:body="props">
          <q-tr :props="props" :class="!props.row.active ? 'bg-red-1' : ''" @click="onRowClicked(props.row)"
            class="cursor-pointer">
            <q-td key="username" :props="props">
              <div v-if="$q.screen.gt.sm">
                {{ props.row.username }}
              </div>
              <div v-else>
                <q-icon name="person" />
                {{ props.row.name }} ({{ props.row.username }})
              </div>
              <template v-if="!$q.screen.gt.sm">
                <div class="elipsis" style="max-width: 200px;"><q-icon name="assignment_ind" />
                  Role: <span>{{ $CONSTANTS.USER_ROLES[props.row.role] }}</span>
                </div>
                <div v-if="props.row.parent">
                  <q-icon name="supervisor_account" /> Supervisor: {{ props.row.parent.name }}
                </div>
                <div v-if="props.row.work_area">
                  <q-icon name="explore" /> Area Kerja: {{ props.row.work_area }}
                </div>
              </template>
            </q-td>
            <q-td key="name" :props="props">
              {{ props.row.name }}
            </q-td>
            <q-td key="role" :props="props" align="center">
              <span>{{ $CONSTANTS.USER_ROLES[props.row.role] }}</span>
            </q-td>
            <q-td key="work_area" :props="props">
              {{ props.row.work_area }}
            </q-td>
            <q-td key="parent" :props="props">
              {{ props.row.parent ? props.row.parent.name : '' }}
            </q-td>
            <q-td key="action" :props="props">
              <div class="flex justify-end">
                <q-btn
                  :disable="props.row.id == currentUser.id || props.row.username == 'admin' || $page.props.auth.user.role != $CONSTANTS.USER_ROLE_ADMIN"
                  icon="more_vert" dense flat style="height: 40px; width: 30px" @click.stop>
                  <q-menu anchor="bottom right" self="top right" transition-show="scale" transition-hide="scale">
                    <q-list style="width: 200px">
                      <q-item clickable v-ripple v-close-popup
                        @click.stop="router.get(route('admin.user.duplicate', props.row.id))">
                        <q-item-section avatar>
                          <q-icon name="file_copy" />
                        </q-item-section>
                        <q-item-section icon="copy"> Duplikat </q-item-section>
                      </q-item>
                      <q-item clickable v-ripple v-close-popup
                        @click.stop="router.get(route('admin.user.edit', props.row.id))">
                        <q-item-section avatar>
                          <q-icon name="edit" />
                        </q-item-section>
                        <q-item-section icon="edit">{{ $t("edit_user") }}</q-item-section>
                      </q-item>
                      <q-item @click.stop="deleteItem(props.row)" clickable v-ripple v-close-popup>
                        <q-item-section avatar>
                          <q-icon name="delete_forever" />
                        </q-item-section>
                        <q-item-section>{{ $t("delete_user") }}</q-item-section>
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
