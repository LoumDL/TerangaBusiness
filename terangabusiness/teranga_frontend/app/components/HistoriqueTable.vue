<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b border-gray-100">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
          <span class="mr-2">📋</span>
          Historique des Transactions
          <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
            {{ transactions.length }}
          </span>
        </h2>
      </div>
    </div>

    <!-- Skeleton -->
    <div v-if="isLoading" class="p-6 space-y-3">
      <div
        v-for="i in 5"
        :key="i"
        class="flex items-center justify-between p-3 bg-gray-50 rounded-xl animate-pulse"
      >
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gray-200 rounded-xl" />
          <div>
            <div class="h-4 w-40 bg-gray-200 rounded mb-1" />
            <div class="h-3 w-24 bg-gray-200 rounded" />
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <div class="h-4 w-24 bg-gray-200 rounded" />
          <div class="h-5 w-16 bg-gray-200 rounded-full" />
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="transactions.length === 0" class="p-16 text-center">
      <div class="text-6xl mb-4">📭</div>
      <p class="text-gray-600 font-medium">Aucune transaction trouvée</p>
      <p class="text-gray-400 text-sm mt-1">Modifiez vos filtres ou effectuez votre première cotisation.</p>
    </div>

    <!-- Desktop table -->
    <div v-else class="hidden sm:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr
            v-for="t in transactions"
            :key="t.id"
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ formatDate(t.date) }}</td>
            <td class="px-6 py-4">
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold"
                :class="t.type === 'COTISATION' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700'"
              >
                {{ t.type === 'COTISATION' ? '📅 Cotisation' : '💳 Paiement' }}
              </span>
            </td>
            <td class="px-6 py-4 text-gray-700 max-w-xs truncate">{{ t.description }}</td>
            <td class="px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">
              {{ formatMontantFCFA(t.montant) }}
            </td>
            <td class="px-6 py-4 text-center">
              <CotisationBadge :statut="t.statut" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile cards -->
    <div class="sm:hidden divide-y divide-gray-50">
      <div v-for="t in transactions" :key="t.id" class="p-4">
        <div class="flex items-start justify-between">
          <div class="flex items-center space-x-3 min-w-0">
            <div
              class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-sm"
              :class="t.type === 'COTISATION' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'"
            >
              {{ t.type === 'COTISATION' ? '📅' : '💳' }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ t.description }}</p>
              <p class="text-xs text-gray-400">{{ formatDate(t.date) }}</p>
            </div>
          </div>
          <div class="ml-3 text-right flex-shrink-0">
            <p class="text-sm font-semibold text-gray-900">{{ formatMontantFCFA(t.montant) }}</p>
            <CotisationBadge :statut="t.statut" class="mt-1" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Transaction } from '~/types'
import { useFormatCurrency } from '~/composables/useFormatCurrency'
import { useFormatDate } from '~/composables/useFormatDate'

defineProps<{
  transactions: Transaction[]
  isLoading: boolean
}>()

const { formatMontantFCFA } = useFormatCurrency()
const { formatDate } = useFormatDate()
</script>
