<template>
  <div class="space-y-6">
    <div class="h-36 bg-gray-200 rounded-2xl animate-pulse" />
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <div class="h-5 w-48 bg-gray-200 rounded animate-pulse mb-4" />
      <div class="space-y-3">
        <div v-for="i in 4" :key="i" class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-200 rounded-xl animate-pulse" />
            <div>
              <div class="h-4 w-32 bg-gray-200 rounded animate-pulse mb-1" />
              <div class="h-3 w-20 bg-gray-200 rounded animate-pulse" />
            </div>
          </div>
          <div class="h-5 w-24 bg-gray-200 rounded animate-pulse" />
        </div>
      </div>
    </div>
  </div>
</template>
