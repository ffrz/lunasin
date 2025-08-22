<script setup>
import { useApiForm } from '@/composables/useApiForm';
import { createOptions } from '@/helpers/options';
import { Notify } from 'quasar';
import { ref } from 'vue';

const props = defineProps({
  modelValue: Boolean,
});
const nameInput = ref(null);
const emit = defineEmits(['update:modelValue', 'created']);
const title = `Tambah Pihak Baru`;
const form = useApiForm({
  name: '',
  type: 'personal',
  phone: '',
  address: '',
  active: true,
  notes: '',
});

const types = createOptions(window.CONSTANTS.PARTY_TYPES);

const submit = () => {
  form.post(route('app.party.save', {response: 'json'}), {
    preserveScroll: true,
    onSuccess: (response) => {
      form.reset();
      emit('created', response.data);
      Notify.create({
        message: response.message || 'Pihak baru telah ditambahkan',
      });
      closeDialog();
    },
    onError: () => {
      nameInput.value.focus();
      nameInput.value.select();
    }
  });
};

const closeDialog = () => {
  emit('update:modelValue', false);
};

</script>


<template>
  <q-dialog :model-value="modelValue">
    <q-card style="min-width: 350px">
      <q-card-section>
        <div class="text-h6">{{ title }}</div>
      </q-card-section>

      <q-form @submit.prevent="submit">
        <q-card-section class="q-pt-none">
          <q-input
            ref="nameInput"
            dense
            v-model="form.name"
            label="Nama Pihak"
            autofocus
            :error="!!form.errors.name"
            :error-message="form.errors.name"
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
            hide-bottom-space
          />
          <q-input
            v-model.trim="form.phone"
            type="text"
            label="No Telepon"
            lazy-rules
            :disable="form.processing"
            :error="!!form.errors.phone"
            :error-message="form.errors.phone"
            hide-bottom-space
          />
          <q-input
            v-model.trim="form.address"
            type="textarea"
            autogrow
            maxlength="255"
            label="Alamat"
            lazy-rules
            :disable="form.processing"
            :error="!!form.errors.address"
            :error-message="form.address"
            hide-bottom-space
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" v-close-popup />
          <q-btn flat label="Simpan" color="primary" type="submit" :loading="form.processing" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>
