<script setup>
import { useApiForm } from '@/composables/useApiForm';
import { Notify } from 'quasar';
import { ref } from 'vue';

const props = defineProps({
  modelValue: Boolean,
});
const nameInput = ref(null);
const emit = defineEmits(['update:modelValue', 'created']);
const title = `Tambah Kategori Baru`;
const form = useApiForm({
  name: '',
});

const submit = () => {
  form.post(route('app.transaction-category.save', {response: 'json'}), {
    preserveScroll: true,
    onSuccess: (response) => {
      form.reset();
      emit('created', response.data);
      Notify.create({
        message: response.message || 'Kategori baru telah ditambahkan',
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
  form.reset();
  emit('update:modelValue', false);
};

</script>
<template>
  <q-dialog :model-value="modelValue" @hide="closeDialog">
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
            label="Nama Kategori"
            autofocus
            :error="!!form.errors.name"
            :error-message="form.errors.name"
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" @click="closeDialog" />
          <q-btn flat label="Simpan" color="primary" type="submit" :loading="form.processing" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>
