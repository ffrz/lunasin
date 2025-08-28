<script setup>
import {
  dateTimeFromNow,
  formatDateTime,
  formatNumberWithSymbol,
} from "@/helpers/formatter";
import { usePage } from "@inertiajs/vue3";
import LongTextView from "@/components/LongTextView.vue";

const page = usePage();
</script>

<template>
  <div class="text-subtitle1 text-bold text-grey-8">Info Pihak</div>
  <table class="detail">
    <tbody>
      <tr>
        <td style="width: 120px">Nama</td>
        <td style="width: 1px">:</td>
        <td>{{ page.props.data.name }}</td>
      </tr>
      <tr>
        <td>Jenis</td>
        <td>:</td>
        <td>{{ $CONSTANTS.PARTY_TYPES[page.props.data.type] }}</td>
      </tr>
      <tr>
        <td>Total {{ page.props.data.balance >= 0 ? "Piutang" : "Utang" }}</td>
        <td>:</td>
        <td :class="page.props.data.balance >= 0 ? 'text-green' : 'text-red'">
          Rp.
          {{ formatNumberWithSymbol(page.props.data.balance) }}
        </td>
      </tr>
      <tr>
        <td>No Telepon</td>
        <td>:</td>
        <td>{{ page.props.data.phone }}</td>
      </tr>
      <tr>
        <td>Alamat</td>
        <td>:</td>
        <td>{{ page.props.data.address }}</td>
      </tr>
      <tr>

        <td>Status</td>
        <td>:</td>
        <td>{{ page.props.data.active ? "Aktif" : "Tidak Aktif" }}</td>
      </tr>
      <tr>
        <td>Catatan</td>
        <td>:</td>
        <td>{{ page.props.data.notes }}</td>
      </tr>
      <tr v-if="page.props.data.created_datetime">
        <td>Dibuat</td>
        <td>:</td>
        <td>
          {{ dateTimeFromNow(page.props.data.created_datetime) }} -
          {{ formatDateTime(page.props.data.created_datetime) }}
        </td>
      </tr>
      <tr v-if="page.props.data.updated_datetime">
        <td>Diperbarui</td>
        <td>:</td>
        <td>
          {{ dateTimeFromNow(page.props.data.updated_datetime) }} -
          {{ formatDateTime(page.props.data.updated_datetime) }}
        </td>
      </tr>
    </tbody>
  </table>
</template>
