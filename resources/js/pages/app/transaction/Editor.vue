<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { handleSubmit } from "@/helpers/client-req-handler";
import { scrollToFirstErrorField } from "@/helpers/utils";
import LocaleNumberInput from "@/components/LocaleNumberInput.vue";
import DateTimePicker from "@/components/DateTimePicker.vue";
import QuickAddDialog from "@/components/QuickAddDialog.vue";
import dayjs from "dayjs";

const page = usePage();
const title = (!!page.props.data.id ? "Edit" : "Catat") + " Transaksi";
const isQuickAddDialogVisible = ref(false);
const quickAddEntityType = ref(null);

const handleEntityCreated = (newEntity) => {
  router.reload({
    only: ["parties", "categories"],
    onSuccess: () => {
      if (quickAddEntityType.value === "party") {
        form.party_id = newEntity.value;
      } else if (quickAddEntityType.value === "category") {
        form.category_id = newEntity.value;
      }
    },
  });
};

const openQuickAddDialog = (type) => {
  quickAddEntityType.value = type;
  isQuickAddDialogVisible.value = true;
};
const parties = ref(
  page.props.parties.map((party) => ({
    label: party.name,
    value: party.id,
  }))
);

const categories = ref(
  page.props.categories.map((cat) => ({
    label: cat.name,
    value: cat.id,
  }))
);

const filteredCategories = ref([...categories.value]);
const filterCategories = (val, update) => {
  update(() => {
    const search = val?.toLowerCase() ?? "";
    filteredCategories.value = search
      ? categories.value.filter((c) => c.label.toLowerCase().includes(search))
      : [...categories.value];
  });
};

const filteredParties = ref([...parties.value]);
const filterParties = (val, update) => {
  update(() => {
    const search = val?.toLowerCase() ?? "";
    filteredParties.value = search
      ? parties.value.filter((p) => p.label.toLowerCase().includes(search))
      : [...parties.value];
  });
};

const types = Object.entries(window.CONSTANTS.TRANSACTION_TYPES).map(
  ([value, label]) => ({
    value,
    label,
  })
);

const form = useForm({
  id: page.props.data.id,
  party_id: page.props.data.party_id,
  category_id: page.props.data.category_id,
  type: page.props.data.type,
  datetime: dayjs(page.props.data.datetime).format("YYYY-MM-DD HH:mm:ss"),
  notes: page.props.data.notes,
  amount: parseFloat(page.props.data.amount),
});

const submit = () => handleSubmit({ form, url: route("app.transaction.save") });
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <template #left-button>
      <div class="q-gutter-sm">
        <q-btn
          icon="arrow_back"
          dense
          color="grey-7"
          flat
          rounded
          @click="$inertia.get(route('app.transaction.index'))"
        />
      </div>
    </template>
    <q-page class="row justify-center">
      <div class="col col-lg-6 q-pa-sm">
        <q-form
          class="row"
          @submit.prevent="submit"
          @validation-error="scrollToFirstErrorField"
        >
          <q-card square flat bordered class="col">
            <q-card-section class="q-pt-none">
              <input type="hidden" name="id" v-model="form.id" />
              <date-time-picker
                v-model="form.datetime"
                label="Waktu"
                :error="!!form.errors.datetime"
                :disable="form.processing"
              />
              <q-select
                autofocus
                v-model="form.type"
                label="Jenis"
                :options="types"
                map-options
                emit-value
                :error="!!form.errors.type"
                :disable="form.processing"
                :errorMessage="form.errors.type"
              >
              </q-select>
              <q-select
                class="custom-select"
                v-model="form.party_id"
                :label="form.type == 'debt' ? 'Dari' : 'Ke'"
                :options="filteredParties"
                @filter="filterParties"
                use-input
                input-debounce="300"
                map-options
                emit-value
                :errorMessage="form.errors.party_id"
                :error="!!form.errors.party_id"
                :disable="form.processing"
              >
                <template v-slot:append>
                  <q-btn
                    icon="add"
                    dense
                    round
                    flat
                    @click.stop.prevent="openQuickAddDialog('party')"
                  />
                </template>
              </q-select>

              <q-select
                class="custom-select"
                v-model="form.category_id"
                label="Kategori"
                :options="filteredCategories"
                @filter="filterCategories"
                use-input
                input-debounce="300"
                map-options
                emit-value
                :errorMessage="form.errors.category_id"
                :error="!!form.errors.category_id"
                :disable="form.processing"
              >
                <template v-slot:append>
                  <q-btn
                    icon="add"
                    dense
                    round
                    flat
                    @click.stop.prevent="openQuickAddDialog('category')"
                  />
                </template>
              </q-select>

              <LocaleNumberInput
                v-model:modelValue="form.amount"
                :label="
                  form.type == 'adjustment' ? 'Saldo Seharusnya' : 'Jumlah'
                "
                lazyRules
                :disable="form.processing"
                :error="!!form.errors.amount"
                :errorMessage="form.errors.amount"
                :rules="[]"
                hide-button-space
              />
              <q-input
                v-model.trim="form.notes"
                type="textarea"
                autogrow
                counter
                maxlength="255"
                label="Keterangan"
                lazy-rules
                :disable="form.processing"
                :error="!!form.errors.notes"
                :error-message="form.errors.notes"
                :rules="[]"
                hide-button-space
              />
            </q-card-section>
            <q-card-section class="q-gutter-sm">
              <q-btn
                icon="save"
                type="submit"
                label="Simpan"
                color="primary"
                :disable="form.processing"
              />
              <q-btn
                icon="cancel"
                label="Batal"
                :disable="form.processing"
                @click="$goBack()"
              />
            </q-card-section>
          </q-card>
        </q-form>
      </div>
      <QuickAddDialog
        v-model="isQuickAddDialogVisible"
        :entity-type="quickAddEntityType"
        @created="handleEntityCreated"
      />
    </q-page>
  </authenticated-layout>
</template>
