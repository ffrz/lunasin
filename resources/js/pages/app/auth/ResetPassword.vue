<script setup>
import { validateEmail } from "@/helpers/validations";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const showPassword = ref(false);

const props = defineProps({
  email: {
    type: String,
    required: true,
  },
  token: {
    type: String,
    required: true,
  },
});

const form = useForm({
  token: props.token,
  email: props.email,
  password: "",
  password_confirmation: "",
});

const submit = () => {
  form.clearErrors();
  form.post(route("app.auth.reset-password"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <i-head title="Atur Ulang Kata Sandi" />
  <guest-layout>
    <q-page class="row justify-center items-center">
      <div class="column">
        <div class="row">
          <q-form class="q-gutter-md" @submit.prevent="submit">
            <q-card square bordered class="q-pa-md shadow-1">
              <q-card-section>
                <h6 class="q-my-sm text-center">Atur Ulang Kata Sandi</h6>
              </q-card-section>
              <q-card-section>
                <q-input
                  square
                  v-model.trim="form.email"
                  label="Email"
                  lazy-rules
                  :error="!!form.errors.email"
                  :error-message="form.errors.email"
                  :rules="[
                    (val) => validateEmail(val) || 'Format Email tidak valid',
                  ]"
                >
                  <template v-slot:append>
                    <q-icon name="email" />
                  </template>
                </q-input>
                <q-input
                  square
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  label="Kata Sandi"
                  :error="!!form.errors.password"
                  :error-message="form.errors.password"
                  lazy-rules
                  :rules="[
                    (val) =>
                      (val && val.length > 0) || 'Masukkan kata sandi baru',
                  ]"
                >
                  <template v-slot:append>
                    <q-btn
                      dense
                      flat
                      round
                      @click="showPassword = !showPassword"
                      ><q-icon :name="showPassword ? 'key_off' : 'key'"
                    /></q-btn>
                  </template>
                </q-input>
                <q-input
                  square
                  v-model="form.password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  label="Ulangi Kata Sandi"
                  :error="!!form.errors.password_confirmation"
                  :error-message="form.errors.password_confirmation"
                  lazy-rules
                  :rules="[
                    (val) =>
                      (val && val.length > 0) || 'Konfirmasi kata sandi baru.',
                    () =>
                      form.password == form.password_confirmation ||
                      'Kata sandi yang dikonfirmasi salah.',
                  ]"
                >
                  <template v-slot:append>
                    <q-btn
                      dense
                      flat
                      round
                      @click="showPassword = !showPassword"
                      ><q-icon :name="showPassword ? 'key_off' : 'key'"
                    /></q-btn>
                  </template>
                </q-input>
              </q-card-section>
              <q-card-actions>
                <q-btn
                  icon="send"
                  type="submit"
                  color="primary"
                  class="full-width"
                  label="Reset Password"
                />
              </q-card-actions>
              <q-card-section class="text-center q-pa-none q-mt-md">
                <p class="q-my-xs text-grey-7">
                  <i-link :href="route('app.auth.login')"
                    >Kembali ke halaman login.</i-link
                  >
                </p>
              </q-card-section>
            </q-card>
          </q-form>
        </div>
      </div>
    </q-page>
  </guest-layout>
</template>
