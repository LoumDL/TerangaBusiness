<template>
  <div class="min-h-screen bg-gray-50">
    <AppHeader />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page title -->
      <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
          <NuxtLink to="/dashboard" class="hover:text-navy-500 transition-colors">Accueil</NuxtLink>
          <span>›</span>
          <span class="text-gray-900 font-medium">Historique</span>
        </div>
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Historique des Transactions</h1>
            <p class="text-sm text-gray-500 mt-1">Toutes vos cotisations et paiements</p>
          </div>
          <button
            v-if="!store.isLoading"
            @click="fetchHistorique"
            class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-navy-600 bg-white border border-gray-200 rounded-xl transition-all hover:border-navy-300"
          >
            <span class="mr-1.5">🔄</span>
            Actualiser
          </button>
        </div>
      </div>

      <!-- Summary stats -->
      <div v-if="!store.isLoading && store.allTransactions.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div
          v-for="stat in stats"
          :key="stat.label"
          class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm"
        >
          <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">{{ stat.label }}</p>
          <p class="text-xl font-bold text-gray-900 mt-1">{{ stat.value }}</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="mb-5">
        <HistoriqueFiltres />
      </div>

      <!-- Table -->
      <HistoriqueTable
        :transactions="store.filteredTransactions"
        :is-loading="store.isLoading"
      />
    </main>
  </div>
</template>

<script setup lang="ts">
import { useHistoriqueStore } from '~/stores/historique'
import { useHistorique } from '~/composables/useHistorique'
import { useFormatCurrency } from '~/composables/useFormatCurrency'

definePageMeta({ middleware: 'auth' })
useHead({ title: 'Historique' })

const store = useHistoriqueStore()
const { fetchHistorique } = useHistorique()
const { formatMontantFCFA } = useFormatCurrency()

const stats = computed(() => {
  const all = store.allTransactions
  const validated = all.filter(t => t.statut === 'VALIDÉ')
  const totalMontant = validated.reduce((sum, t) => sum + t.montant, 0)

  return [
    { label: 'Total transactions', value: all.length },
    { label: 'Validées', value: validated.length },
    { label: 'Rejetées', value: all.filter(t => t.statut === 'REJETÉ').length },
    { label: 'Montant total', value: formatMontantFCFA(totalMontant) },
  ]
})

onMounted(async () => {
  if (store.allTransactions.length === 0) {
    await fetchHistorique()
  }
})
</script>
