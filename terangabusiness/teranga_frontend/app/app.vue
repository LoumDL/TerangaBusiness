<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    <NuxtRouteAnnouncer />
    <Suspense>
      <NuxtPage />
      <template #fallback>
        <div class="min-h-screen bg-gray-50 flex items-center justify-center">
          <div class="flex flex-col items-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-navy-500 flex items-center justify-center animate-pulse">
              <span class="text-white font-bold text-lg">T</span>
            </div>
            <div class="space-y-2 w-48">
              <div class="h-2 bg-gray-200 rounded animate-pulse"></div>
              <div class="h-2 bg-gray-200 rounded animate-pulse w-3/4 mx-auto"></div>
            </div>
          </div>
        </div>
      </template>
    </Suspense>
  </div>
</template>

<script setup lang="ts">
useHead({
  titleTemplate: (title) => title ? `${title} — Teranga Business Hub` : 'Teranga Business Hub',
})
</script>
