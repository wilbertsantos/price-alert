<script setup>
import { containerMaxW } from "@/config.js";
</script>

<template>
  <section class="py-6" :class="containerMaxW">
    <slot />
  </section>
</template>
