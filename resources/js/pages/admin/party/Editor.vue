<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { handleSubmit } from "@/helpers/client-req-handler";
import { createOptions, scrollToFirstErrorField } from "@/helpers/utils";

const page = usePage();
const title = (!!page.props.data.id ? "Edit" : "Tambah") + " Pihak";
const types = createOptions(window.CONSTANTS.PARTY_TYPES);

const form = useForm({
  id: page.props.data.id,
  name: page.props.data.name,
  phone: page.props.data.phone,
  type: page.props.data.type,
  address: page.props.data.address,
  notes: page.props.data.notes,
  active: !!page.props.data.active,
});

const submit = () => handleSubmit({ form, url: route("admin.party.save") });
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <q-page class="row justify-center">
      <div class="col col-md-6 q-pa-sm">
        <q-form
          class="row"
          @submit.prevent="submit"
          @validation-error="scrollToFirstErrorField"
        >
          <q-card square flat bordered class="col">
            <q-inner-loading :showing="form.processing">
              <q-spinner size="50px" color="primary" />
            </q-inner-loading>
            <q-card-section class="q-pt-md">
              <input type="hidden" name="id" v-model="form.id" />
              <q-input
                autofocus
                v-model.trim="form.name"
                label="Nama"
                lazy-rules
                :error="!!form.errors.name"
                :disable="form.processing"
                :error-message="form.errors.name"
                :rules="[
                  (val) => (val && val.length > 0) || 'Nama harus diisi.',
                ]"
              />
              <q-select
                v-model="form.type"
                label="Jenis"
                :options="types"
                map-options
                emit-value
                :error="!!form.errors.type"
                :disable="form.processing"
                :error-message="form.errors.type"
              />
              <q-input
                v-model.trim="form.phone"
                type="text"
                label="No Telepon"
                lazy-rules
                :disable="form.processing"
                :error="!!form.errors.phone"
                :error-message="form.errors.phone"
              />
              <q-input
                v-model.trim="form.address"
                type="textarea"
                autogrow
                counter
                maxlength="1000"
                label="Alamat"
                lazy-rules
                :disable="form.processing"
                :error="!!form.errors.address"
                :error-message="form.address"
              />
              <q-input
                v-model.trim="form.notes"
                type="textarea"
                autogrow
                counter
                maxlength="1000"
                label="Catatan"
                lazy-rules
                :disable="form.processing"
                :error="!!form.errors.notes"
                :error-message="form.errors.notes"
              />
              <div style="margin-left: -10px">
                <q-checkbox
                  class="full-width q-pl-none"
                  v-model="form.active"
                  :disable="form.processing"
                  label="Aktif"
                />
              </div>
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
    </q-page>
  </authenticated-layout>
</template>
