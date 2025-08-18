<script setup>
import { formatNumber } from "@/helpers/formatter";

const props = defineProps({
  topDebtors: {
    type: Array,
    required: true,
  },
  topCreditors: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <div class="row q-col-gutter-sm q-pb-sm">
    <div class="col-md-6 col-12">
      <q-card square bordered flat class="full-width full-height q-pa-md">
        <div class="text-h6 q-mb-sm text-center">
          Top 5 Peminjam (Utang Terbesar)
        </div>
        <q-list separator>
          <q-item v-for="(item, index) in topDebtors" :key="index">
            <q-item-section avatar>
              <q-avatar color="red-8" text-color="white" size="sm">{{
                index + 1
              }}</q-avatar>
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ item.name }}</q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-item-label class="text-red-8">{{
                formatNumber(item.value)
              }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card>
    </div>
    <div class="col-md-6 col-12">
      <q-card square bordered flat class="full-width full-height q-pa-md">
        <div class="text-h6 q-mb-sm text-center">
          Top 5 Pemberi Pinjaman (Piutang Terbesar)
        </div>
        <q-list separator>
          <q-item v-for="(item, index) in topCreditors" :key="index">
            <q-item-section avatar>
              <q-avatar color="green-8" text-color="white" size="sm">{{
                index + 1
              }}</q-avatar>
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ item.name }}</q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-item-label class="text-green-8">{{
                formatNumber(item.value)
              }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card>
    </div>
  </div>
</template>

<style scoped>
/* No specific styles for this component beyond what Quasar provides */
</style>
