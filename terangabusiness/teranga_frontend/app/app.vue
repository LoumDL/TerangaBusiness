<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    <NuxtRouteAnnouncer />
    <NuxtPage />
  </div>
</template>

<script setup lang="ts">
useHead({
  titleTemplate: (title) => title ? `${title} — Teranga Business Hub` : 'Teranga Business Hub',
})
</script>
