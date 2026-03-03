<template>
  <div class="bg-gradient-to-br from-navy-500 to-navy-700 rounded-2xl p-6 text-white shadow-xl">
    <div class="flex items-center justify-between mb-4">
      <div>
        <p class="text-gray-300 text-sm font-medium uppercase tracking-wide">Solde Global Cotisé</p>
        <div v-if="isLoading" class="mt-2 h-9 w-48 bg-white/20 rounded-lg animate-pulse" />
        <p v-else class="text-3xl font-bold mt-1">{{ formatMontantFCFA(solde) }}</p>
      </div>
      <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center">
        <span class="text-2xl">💰</span>
      </div>
    </div>
    <div class="flex items-center text-sm text-gray-300">
      <span class="mr-2">📈</span>
      <span>{{ count }} cotisation{{ count > 1 ? 's' : '' }} validée{{ count > 1 ? 's' : '' }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormatCurrency } from '~/composables/useFormatCurrency'

defineProps<{
  solde: number
  count: number
  isLoading: boolean
}>()

const { formatMontantFCFA } = useFormatCurrency()
</script>
