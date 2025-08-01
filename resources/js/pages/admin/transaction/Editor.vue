<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { handleSubmit } from "@/helpers/client-req-handler";
import { scrollToFirstErrorField } from "@/helpers/utils";
import LocaleNumberInput from "@/components/LocaleNumberInput.vue";
import DateTimePicker from "@/components/DateTimePicker.vue";
import dayjs from "dayjs";
const page = usePage();
const title = (!!page.props.data.id ? "Edit" : "Catat") + " Transaksi Keuangan";

const parties = page.props.parties.map((party) => ({
  label: party.name,
  value: party.id,
}));

const categories = page.props.categories.map((cat) => ({
  label: cat.name,
  value: cat.id,
}));

const types = [
  { label: "Piutang (+)", value: "debt" },
  { label: "Utang (-)", value: "credit" },
  { label: "Penyesuaian", value: "adjustment" },
];

const form = useForm({
  id: page.props.data.id,
  party_id: page.props.data.party_id,
  category_id: page.props.data.category_id,
  type: page.props.data.type,
  datetime: dayjs(page.props.data.datetime).format("YYYY-MM-DD HH:mm:ss"),
  notes: page.props.data.notes,
  amount: parseFloat(page.props.data.amount),
});

const submit = () =>
  handleSubmit({ form, url: route("admin.transaction.save") });
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
          @click="$inertia.get(route('admin.transaction.index'))"
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
                label="Tanggal"
                :error="!!form.errors.datetime"
                :disable="form.processing"
              />
              <q-select
                autofocus
                v-model="form.type"
                label="Jenis Transaksi"
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
                label="Akun Asal"
                :options="parties"
                map-options
                emit-value
                :errorMessage="form.errors.party_id"
                :error="!!form.errors.party_id"
                :disable="form.processing"
              />
              <q-select
                class="custom-select"
                v-model="form.category_id"
                label="Kategori"
                :options="categories"
                map-options
                emit-value
                :errorMessage="form.errors.category_id"
                :error="!!form.errors.category_id"
                :disable="form.processing"
              />
              <LocaleNumberInput
                v-model:modelValue="form.amount"
                label="Jumlah"
                lazyRules
                :disable="form.processing"
                :error="!!form.errors.amount"
                :errorMessage="form.errors.amount"
                :rules="[]"
              />
              <q-input
                v-model.trim="form.notes"
                type="textarea"
                autogrow
                counter
                maxlength="255"
                label="Catatan"
                lazy-rules
                :disable="form.processing"
                :error="!!form.errors.notes"
                :error-message="form.errors.notes"
                :rules="[
                  (val) => (val && val.length > 0) || 'Catatan harus diisi.',
                ]"
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
                @click="router.get(route('admin.transaction.index'))"
              />
            </q-card-section>
          </q-card>
        </q-form>
      </div>
    </q-page>
  </authenticated-layout>
</template>
