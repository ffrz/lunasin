<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue"; // Menambahkan onMounted
import { handleSubmit } from "@/helpers/client-req-handler";
import { scrollToFirstErrorField } from "@/helpers/utils";
import LocaleNumberInput from "@/components/LocaleNumberInput.vue";
import DateTimePicker from "@/components/DateTimePicker.vue";
import QuickAddDialog from "@/components/QuickAddDialog.vue";
import dayjs from "dayjs";
import { useQuasar } from "quasar"; // Menambahkan useQuasar
import { useTransactionCategoryFilter } from "@/composables/useTransactionCategoryFilter";
import { usePartyFilter } from "@/composables/usePartyFilter";

const page = usePage();
const title = (!!page.props.data.id ? "Edit" : "Catat") + " Transaksi";
const isQuickAddDialogVisible = ref(false);
const quickAddEntityType = ref(null);

const form = useForm({
  id: page.props.data.id,
  party_id: page.props.data.party_id,
  category_id: page.props.data.category_id,
  type: page.props.data.type,
  datetime: dayjs(page.props.data.datetime).format("YYYY-MM-DD HH:mm:ss"),
  notes: page.props.data.notes,
  amount: parseFloat(page.props.data.amount),
  // --- START: Penambahan form data untuk Image ---
  image_path: page.props.data.image_path,
  image: null,
  // --- END: Penambahan form data untuk Image ---
});

const handleEntityCreated = (newEntity) => {
  if (quickAddEntityType.value === "party") {
    filteredParties.value.unshift({
      label: newEntity.name,
      value: newEntity.id,
    });
    form.party_id = newEntity.id; // langsung pilih yg baru
  } else if (quickAddEntityType.value === "category") {
    filteredCategories.value.unshift({
      label: newEntity.name,
      value: newEntity.id,
    });
    form.category_id = newEntity.id;
  }

  isQuickAddDialogVisible.value = false;
};

// Di halaman form utama .vue
const quickAddInitialValue = ref("");

const openQuickAddDialog = (type, initialValue = "") => {
  quickAddEntityType.value = type;
  quickAddInitialValue.value = initialValue; // Set nilai awal
  isQuickAddDialogVisible.value = true;
};

const { filteredCategories, filterCategories } = useTransactionCategoryFilter(
  page.props.categories
);
const { filteredParties, filterParties } = usePartyFilter(page.props.parties);

const previousPartyId = ref(form.party_id);
const previousCategoryId = ref(form.category_id);

watch(
  () => form.party_id,
  (newValue, oldValue) => {
    if (newValue !== "new_party") {
      previousPartyId.value = oldValue;
    }

    if (newValue === "new_party") {
      const partyOption = filteredParties.value.find(
        (p) => p.value === "new_party"
      );
      openQuickAddDialog("party", partyOption?.inputValue ?? "");

      form.party_id = previousPartyId.value;
    }
  }
);

watch(
  () => form.category_id,
  (newValue, oldValue) => {
    if (newValue !== "new_category") {
      previousCategoryId.value = oldValue;
    }

    if (newValue === "new_category") {
      const categoryOption = filteredCategories.value.find(
        (c) => c.value === "new_category"
      );
      openQuickAddDialog("category", categoryOption?.inputValue ?? "");
      form.category_id = previousCategoryId.value;
    }
  }
);

const types = Object.entries(window.CONSTANTS.TRANSACTION_TYPES).map(
  ([value, label]) => ({
    value,
    label,
  })
);

// --- START: Penambahan untuk fitur Image ---
const fileInput = ref(null);
const imagePreview = ref("");
const $q = useQuasar(); // Inisialisasi useQuasar

function triggerInput() {
  fileInput.value.click();
}

function onFileChange(event) {
  const file = event.target.files[0];
  if (file) {
    form.image = file;
    // Tampilkan pratinjau gambar baru
    imagePreview.value = URL.createObjectURL(file);
    // Hapus path gambar lama (jika ada)
    form.image_path = null;
  }
}

function clearImage() {
  // Hapus data form dan pratinjau gambar
  form.image = null;
  form.image_path = null;
  imagePreview.value = null;
  // Reset input file agar bisa memilih file yang sama lagi
  fileInput.value.value = null;
}
// --- END: Penambahan untuk fitur Image ---

const submit = () =>
  handleSubmit({
    form,
    // Penting: tambahkan forceFormData: true untuk mengirim file
    // Pastikan server (Laravel) juga dikonfigurasi untuk menerima file
    forceFormData: true,
    url: route("app.transaction.save"),
  });

// --- START: Penambahan logic saat component dimuat ---
onMounted(() => {
  // Jika ada path gambar yang sudah tersimpan, tampilkan pratinjau
  if (form.image_path) {
    imagePreview.value = `/${form.image_path}`;
  }
});
// --- END: Penambahan logic saat component dimuat ---
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

              <QuickAddDialog
                v-model="isQuickAddDialogVisible"
                :entity-type="quickAddEntityType"
                :initial-value="quickAddInitialValue"
                @created="handleEntityCreated"
              />

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
  </authenticated-layout>
</template>
