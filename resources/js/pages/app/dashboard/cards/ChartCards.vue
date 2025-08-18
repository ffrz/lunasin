<script setup>
import { computed } from "vue";
import { formatNumber } from "@/helpers/formatter";

import VChart from "vue-echarts";
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { BarChart, PieChart } from "echarts/charts";
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent,
} from "echarts/components";

use([
  CanvasRenderer,
  BarChart,
  PieChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent,
]);

const props = defineProps({
  monthlyTransactions: {
    type: Object,
    required: true,
  },
  transactionCategoryDistribution: {
    type: Array,
    required: true,
  },
});

const colors = ["#F44336", "#FF9800", "#4CAF50", "#2196F3"];

const barChartOptions = computed(() => ({
  title: {
    text: "Grafik Transaksi Bulanan",
    left: "center",
  },
  tooltip: {
    trigger: "axis",
    formatter: function (params) {
      let result = params[0].name + "<br/>";
      params.forEach(function (item) {
        result +=
          item.marker +
          " " +
          item.seriesName +
          ": " +
          formatNumber(item.value) +
          "<br/>";
      });
      return result;
    },
  },
  xAxis: {
    type: "category",
    data: props.monthlyTransactions.labels,
    axisLabel: {
      color: "#616161",
    },
  },
  yAxis: {
    type: "value",
    axisLabel: {
      formatter: (value) => formatNumber(value),
      color: "#616161",
    },
  },
  series: [
    {
      name: "Total Transaksi",
      type: "bar",
      data: props.monthlyTransactions.data,
      itemStyle: {
        borderRadius: [5, 5, 0, 0],
        color: colors[2],
      },
    },
  ],
}));

const pieChartOptions = computed(() => ({
  title: {
    text: "Distribusi Kategori Transaksi",
    left: "center",
  },
  tooltip: {
    trigger: "item",
    formatter: function (params) {
      return (
        params.name +
        "<br/>" +
        params.seriesName +
        ": " +
        formatNumber(params.value) +
        " (" +
        params.percent +
        "%)"
      );
    },
  },
  legend: {
    orient: "vertical",
    left: "left",
    textStyle: {
      color: "#616161",
    },
  },
  series: [
    {
      name: "Jumlah Transaksi",
      type: "pie",
      radius: ["40%", "70%"],
      data: props.transactionCategoryDistribution,
      itemStyle: {
        borderRadius: 5,
        borderColor: "#fff",
        borderWidth: 2,
        color: (params) => {
          return colors[params.dataIndex % colors.length];
        },
      },
      emphasis: {
        itemStyle: {
          shadowBlur: 10,
          shadowOffsetX: 0,
          shadowColor: "rgba(0, 0, 0, 0.5)",
        },
      },
    },
  ],
}));
</script>

<template>
  <div class="row q-col-gutter-sm q-pb-sm">
    <div class="col-md-6 col-12">
      <q-card square bordered flat class="full-width q-pa-md text-center">
        <VChart class="chart" :option="barChartOptions" autoresize />
      </q-card>
    </div>
    <div class="col-md-6 col-12">
      <q-card square bordered flat class="full-width q-pa-md">
        <VChart class="chart" :option="pieChartOptions" autoresize />
      </q-card>
    </div>
  </div>
</template>

<style scoped>
.chart {
  height: 350px;
}
</style>
