<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import { handleSubmit } from "@/helpers/client-req-handler";
import { scrollToFirstErrorField } from "@/helpers/utils";
import { useQuasar } from "quasar";
import { useTransactionCategoryFilter } from "@/composables/useTransactionCategoryFilter";
import { usePartyFilter } from "@/composables/usePartyFilter";
import { formatDateTimeForEditing } from "@/helpers/formatter";

import LocaleNumberInput from "@/components/LocaleNumberInput.vue";
import DateTimePicker from "@/components/DateTimePicker.vue";
import AddCategoryDialog from "@/components/AddCategoryDialog.vue";
import AddPartyDialog from "@/components/AddPartyDialog.vue";

const page = usePage();
const title = (!!page.props.data.id ? "Edit" : "Catat") + " Transaksi";
const showAddCategoryDialog = ref(false);
const showAddPartyDialog = ref(false);

const form = useForm({
  id: page.props.data.id,
  party_id: page.props.data.party_id,
  category_id: page.props.data.category_id,
  type: page.props.data.type,
  datetime: formatDateTimeForEditing(page.props.data.datetime),
  notes: page.props.data.notes,
  amount: parseFloat(page.props.data.amount),
  image_path: page.props.data.image_path,
  image: null,
});

const { filteredCategories, filterCategories, addCategory } = useTransactionCategoryFilter([
  { id: 'new', name: '<< Kategori Baru ... >>'},
  ...page.props.categories
]);

const { filteredParties, filterParties, addParty } = usePartyFilter([
  { id: 'new', name: '<< Pihak Baru ... >>'},
  ...page.props.parties
]);


const handleCategoryCreated = (newCategory) => {
  form.category_id = addCategory(newCategory);
};

const handlePartyCreated = (newParty) => {
  form.party_id = addParty(newParty);
};

const onCategorySelected = (value) => {
  console.log('changed', value, showAddCategoryDialog.value);
  if (value === 'new') {
    form.category_id = null;
    showAddCategoryDialog.value = true;
  } else {
    form.category_id = value;
  }
};

const onPartySelected = (value) => {
  if (value === 'new') {
    form.party_id = null;
    showAddPartyDialog.value = true;
  } else {
    form.party_id = value;
  }
};

const types = Object.entries(window.CONSTANTS.TRANSACTION_TYPES).map(
  ([value, label]) => ({
    value,
    label,
  })
);

const fileInput = ref(null);
const imagePreview = ref("");
const $q = useQuasar();
function triggerInput() {
  fileInput.value.click();
}

function onFileChange(event) {
  const file = event.target.files[0];
  if (file) {
    form.image = file;
    imagePreview.value = URL.createObjectURL(file);
    form.image_path = null;
  }
}

function clearImage() {
  form.image = null;
  form.image_path = null;
  imagePreview.value = null;
  fileInput.value.value = null;
}

const submit = () =>
  handleSubmit({
    form,
    forceFormData: true,
    url: route("app.transaction.save"),
  });

onMounted(() => {
  if (form.image_path) {
    imagePreview.value = `/${form.image_path}`;
  }
});
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
          @click="$goBack()"
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
                hide-bottom-space
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
                :rules="[
                  (val) => (val && val.length > 0) || 'Jenis harus diisi.',
                ]"
                hide-bottom-space
              >
              </q-select>
              <q-select
                class="custom-select"
                v-model="form.party_id"
                :label="
                  form.type == 'adjustment'
                    ? 'Ke / Dari'
                    : form.type == 'loan_given' ||
                      form.type == 'payable_payment'
                    ? 'Ke'
                    : 'Dari'
                "
                :options="filteredParties"
                @filter="filterParties"
                @update:model-value="onPartySelected"
                use-input
                input-debounce="300"
                map-options
                emit-value
                :errorMessage="form.errors.party_id"
                :error="!!form.errors.party_id"
                :disable="form.processing"

                clearable
                hide-bottom-space
              >
              </q-select>

              <q-select
                class="custom-select"
                v-model="form.category_id"
                label="Kategori"
                :options="filteredCategories"
                @filter="filterCategories"
                @update:model-value="onCategorySelected"
                use-input
                input-debounce="300"
                map-options
                emit-value
                :errorMessage="form.errors.category_id"
                :error="!!form.errors.category_id"
                :disable="form.processing"
                clearable
                hide-bottom-space
              >
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
                :rules="[
                  (vak) => (vak !== null && vak !== undefined && vak !== '' && !isNaN(vak)) || 'Jumlah harus diisi.',
                  (val) => (val > 0) || 'Jumlah harus lebih dari 0.',
                ]"
                :allowNegative="form.type == 'adjustment'"
                hide-bottom-space
              />
              <q-input
                v-model.trim="form.notes"
                type="textarea"
                autogrow
                maxlength="255"
                label="Keterangan"
                lazy-rules
                :disable="form.processing"
                :error="!!form.errors.notes"
                :error-message="form.errors.notes"
                :rules="[]"
                hide-bottom-space
              />

              <div class="q-pt-md">
                <div class="text-subtitle2 text-bold text-grey-9">
                  Foto Lampiran:
                </div>
                <div class="q-gutter-x-sm q-mt-sm">
                  <q-btn
                    label="Pilih Foto"
                    size="sm"
                    @click.prevent="triggerInput"
                    color="secondary"
                    icon="add_a_photo"
                    :disable="form.processing"
                  />
                  <q-btn
                    size="sm"
                    icon="close"
                    label="Buang"
                    :disable="
                      form.processing || (!imagePreview && !form.image_path)
                    "
                    color="red"
                    @click.prevent="clearImage"
                  />
                  <input
                    type="file"
                    ref="fileInput"
                    accept="image/*"
                    style="display: none"
                    @change="onFileChange"
                  />
                </div>

                <div
                  v-if="form.errors.image || form.errors.image_path"
                  class="text-negative q-mt-sm"
                >
                  {{ form.errors.image || form.errors.image_path }}
                </div>

                <div class="q-mt-md">
                  <q-img
                    v-if="imagePreview"
                    :src="imagePreview"
                    style="
                      max-width: 500px;
                      border: 1px solid #ddd;
                      border-radius: 4px;
                    "
                  >
                    <template v-slot:error>
                      <div class="text-negative text-center q-pa-md">
                        Gambar tidak tersedia
                      </div>
                    </template>
                  </q-img>
                </div>
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

    <AddCategoryDialog
      v-model="showAddCategoryDialog"
      @created="handleCategoryCreated"
    />

    <AddPartyDialog
      v-model="showAddPartyDialog"
      @created="handlePartyCreated"
    />

  </authenticated-layout>
</template>
