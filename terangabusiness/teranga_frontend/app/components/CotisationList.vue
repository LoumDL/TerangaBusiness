<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
      <h2 class="text-lg font-semibold text-gray-900 flex items-center">
        <span class="mr-2">📅</span>
        Mes Cotisations Régulières
      </h2>
    </div>

    <!-- Skeleton -->
    <div v-if="isLoading" class="p-6 space-y-3">
      <div v-for="i in 4" :key="i" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 animate-pulse">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gray-200 rounded-lg" />
          <div class="space-y-1.5">
            <div class="h-4 w-36 bg-gray-200 rounded" />
            <div class="h-3 w-24 bg-gray-200 rounded" />
          </div>
        </div>
        <div class="h-6 w-20 bg-gray-200 rounded-full" />
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="cotisations.length === 0" class="p-12 text-center">
      <div class="text-5xl mb-3">📭</div>
      <p class="text-gray-500 text-sm">Aucune cotisation enregistrée.</p>
    </div>

    <!-- List -->
    <div v-else class="divide-y divide-gray-50">
      <div
        v-for="cot in cotisations"
        :key="cot.id"
        class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors"
      >
        <div class="flex items-center space-x-3 min-w-0">
          <div class="w-10 h-10 bg-navy-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <span class="text-navy-500 font-bold text-xs">{{ getPeriodeAbbrev(cot.periode) }}</span>
          </div>
          <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-900 truncate">{{ formatPeriode(cot.periode) }}</p>
            <p class="text-xs text-gray-500">{{ formatDate(cot.created_at) }}</p>
          </div>
        </div>
        <div class="flex items-center space-x-3 ml-4 flex-shrink-0">
          <span class="text-sm font-semibold text-gray-900">{{ formatMontantFCFA(cot.montant) }}</span>
          <CotisationBadge :statut="cot.statut" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Cotisation } from '~/types'
import { useFormatCurrency } from '~/composables/useFormatCurrency'
import { useFormatDate } from '~/composables/useFormatDate'

defineProps<{
  cotisations: Cotisation[]
  isLoading: boolean
}>()

const { formatMontantFCFA } = useFormatCurrency()
const { formatDate, formatPeriode } = useFormatDate()

const getPeriodeAbbrev = (periode: string) => {
  const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc']
  const [, month] = periode.split('-')
  return months[Number(month) - 1] || periode
}
</script>
