<script setup>
import { defineComponent } from "vue";

defineComponent({
  name: "AuthLayout",
});
</script>
<style scoped>
.q-toolbar a {
  color: #ddd;
}

.q-toolbar a:hover {
  color: #fff;
}
</style>

<template>
  <q-layout view="hHh lpR fff" style="background: #f5f5f5">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar style="max-width: 980px; margin: 0 auto">
        <q-toolbar-title align="center">
          <q-avatar square size="auto" style="padding: 0">
            <img
              src="/assets/img/app-logo-white.png"
              style="
                height: auto;
                width: auto;
                max-height: 40px;
                object-fit: contain;
              "
            />
          </q-avatar>
          <a class="q-pl-md text-white" :href="route('home')">{{
            $config.APP_NAME
          }}</a>
        </q-toolbar-title>
      </q-toolbar>
    </q-header>
    <q-page-container style="max-width: 1024px; margin: 20px auto 0">
      <slot></slot>
    </q-page-container>
    <q-footer class="transparent">
      <div class="q-mt-lg q-mb-md justify-center items-center flex text-grey-6">
        <span
          >&copy; 2025 - {{ $config.APP_NAME }} v{{
            $config.APP_VERSION_STR
          }}</span
        >
      </div>
    </q-footer>
  </q-layout>
</template>
