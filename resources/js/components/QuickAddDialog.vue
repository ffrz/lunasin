<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
  modelValue: Boolean,
  entityType: String,
  initialValue: String,
});

const emit = defineEmits(['update:modelValue', 'created']);

const title = computed(() =>
  `Tambah ${props.entityType === 'party' ? 'Pihak' : 'Kategori'} Baru`
);

const form = useForm({
  name: '',
});

const submit = () => {
  const url =
    props.entityType === 'party'
      ? route('app.party.save')
      : route('app.transaction-category.save');

  form.post(url, {
    preserveScroll: true,
    onSuccess: (page) => {
      // ambil entity baru dari backend
      const newEntity =
        props.entityType === 'party'
          ? page.props.new_party
          : page.props.new_category;

      emit('created', newEntity); // ⬅️ kirim ke parent
      closeDialog();
    },
    onFinish: () => {
      form.reset();
    },
  });
};

watch(
  () => props.modelValue,
  (isShown) => {
    if (isShown && props.initialValue) {
      form.name = props.initialValue;
    }
  }
);

const closeDialog = () => {
  emit('update:modelValue', false);
};
</script>


<template>
  <q-dialog :model-value="modelValue" @update:model-value="closeDialog">
    <q-card style="min-width: 350px">
      <q-card-section>
        <div class="text-h6">{{ title }}</div>
      </q-card-section>

      <q-form @submit.prevent="submit">
        <q-card-section class="q-pt-none">
          <q-input
            dense
            v-model="form.name"
            label="Nama"
            autofocus
            :error="!!form.errors.name"
            :error-message="form.errors.name"
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
