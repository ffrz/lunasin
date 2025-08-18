<script setup>
import { computed } from "vue";
import { formatNumber } from "@/helpers/formatter";

const props = defineProps({
  summary: {
    type: Object,
    required: true,
  },
});

const summaryItems = computed(() => [
  {
    label: "Balance",
    value: formatNumber(props.summary.balance),
    icon: "account_balance_wallet",
    color: "indigo-8",
    bgColor: "#e8eaf6",
  },
  {
    label: "Total Utang",
    value: formatNumber(props.summary.totalDebt),
    icon: "money_off",
    color: "red-8",
    bgColor: "#ffebee",
  },
  {
    label: "Total Piutang",
    value: formatNumber(props.summary.totalReceivable),
    icon: "attach_money",
    color: "green-8",
    bgColor: "#e8f5e9",
  },
  // {
  //   label: "Total Pihak",
  //   value: formatNumber(props.summary.totalParties),
  //   icon: "groups",
  //   color: "blue-8",
  //   bgColor: "#e3f2fd",
  // },
  // {
  //   label: "Transaksi Berjalan",
  //   value: formatNumber(props.summary.totalTransactions),
  //   icon: "swap_horiz",
  //   color: "purple-8",
  //   bgColor: "#f3e5f5",
  // },
  // {
  //   label: "Piutang Jatuh Tempo",
  //   value: formatNumber(props.summary.receivableDue),
  //   icon: "alarm_on",
  //   color: "orange-8",
  //   bgColor: "#fff3e0",
  // },
  // {
  //   label: "Utang Jatuh Tempo",
  //   value: formatNumber(props.summary.debtDue),
  //   icon: "alarm_on",
  //   color: "deep-orange-8",
  //   bgColor: "#fbe9e7",
  // },
]);
</script>

<template>
  <div class="q-pb-sm">
    <div class="text-h6 text-bold text-grey-8 q-mb-sm text-center">
      Statistik Ringkasan
    </div>
    <div class="row q-col-gutter-sm">
      <div
        v-for="(item, index) in summaryItems"
        :key="index"
        class="col-12 col-md-4 col-lg-4"
      >
        <q-card square bordered flat class="q-pa-md" style="width: 100%">
          <div class="row items-center no-wrap">
            <q-avatar
              :icon="item.icon"
              :color="item.color"
              text-color="white"
            />
            <div class="q-ml-md">
              <div class="text-subtitle2 text-grey-8">
                {{ item.label }}
              </div>
              <div class="text-h6 text-weight-bold">{{ item.value }}</div>
            </div>
          </div>
        </q-card>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* No specific styles for this component beyond what Quasar provides */
</style>
