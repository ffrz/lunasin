<script setup>
import ImageViewer from "@/components/ImageViewer.vue";
import {
  dateTimeFromNow,
  formatDateTime,
  formatNumberWithSymbol,
} from "@/helpers/formatter";
import { router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const page = usePage();
const title = "Rincian Transaksi";
const showViewer = ref(false);
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
    <template #right-button>
      <div class="q-gutter-sm">
        <q-btn
          icon="edit"
          dense
          color="primary"
          @click="
            router.get(
              route('app.transaction.edit', { id: page.props.data.id })
            )
          "
        />
      </div>
    </template>
    <div class="row justify-center">
      <div class="col col-lg-6 q-pa-sm">
        <div class="row">
          <q-card square flat bordered class="col">
            <q-card-section>
              <div class="text-subtitle1 text-bold text-grey-9">
                Rincian Transaksi
              </div>
              <table class="detail">
                <tbody>
                  <tr>
                    <td style="width: 125px">ID</td>
                    <td style="width: 1px">:</td>
                    <td>#{{ page.props.data.id }}</td>
                  </tr>
                  <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td>
                      {{ dateTimeFromNow(page.props.data.datetime) }} -
                      {{ formatDateTime(page.props.data.datetime) }}
                    </td>
                  </tr>
                  <tr>
                    <td>Pihak</td>
                    <td>:</td>
                    <td>
                      {{ page.props.data.party.name }}
                    </td>
                  </tr>
                  <tr>
                    <td>Kategori</td>
                    <td>:</td>
                    <td>
                      {{ page.props.data.category.name }}
                    </td>
                  </tr>
                  <tr>
                    <td>Jenis</td>
                    <td>:</td>
                    <td>
                      {{ $CONSTANTS.TRANSACTION_TYPES[page.props.data.type] }}
                    </td>
                  </tr>
                  <tr>
                    <td>Jumlah</td>
                    <td>:</td>
                    <td>
                      Rp.
                      {{ formatNumberWithSymbol(page.props.data.amount) }}
                    </td>
                  </tr>
                  <tr>
                    <td>Catatan</td>
                    <td>:</td>
                    <td>
                      {{ page.props.data.notes }}
                    </td>
                  </tr>
                  <tr v-if="page.props.data.image_path">
                    <td colspan="3" class="bg-white">
                      <div class="q-mt-md">
                        Lampiran:<br />
                        <q-img
                          :src="`/${page.props.data.image_path}`"
                          class="q-mt-none"
                          style="max-width: 500px"
                          :style="{ border: '1px solid #ddd' }"
                          @click="showViewer = true"
                        />
                      </div>
                    </td>
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
              <ImageViewer
                v-model="showViewer"
                :imageUrl="`/${page.props.data.image_path}`"
              />
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
  </authenticated-layout>
</template>
