<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
      <span class="mr-2">🔍</span>
      Filtrer l'historique
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <!-- Filtre par jour -->
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Par jour</label>
        <input
          v-model="filterDay"
          type="date"
          class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-navy-400 focus:border-transparent outline-none"
          @change="applyDayFilter"
        />
      </div>

      <!-- Filtre par mois -->
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Par mois</label>
        <input
          v-model="filterMonth"
          type="month"
          class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-navy-400 focus:border-transparent outline-none"
          @change="applyMonthFilter"
        />
      </div>

      <!-- Filtre par année -->
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Par année</label>
        <select
          v-model="filterYear"
          class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-navy-400 focus:border-transparent outline-none"
          @change="applyYearFilter"
        >
          <option value="">Toutes les années</option>
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
      </div>
    </div>

    <!-- Active filter indicator + Reset -->
    <div v-if="hasActiveFilter" class="mt-4 flex items-center justify-between">
      <div class="flex items-center space-x-2 text-xs text-navy-600 bg-navy-50 px-3 py-1.5 rounded-lg">
        <span>🔎</span>
        <span class="font-medium">Filtre actif : {{ activeFilterLabel }}</span>
      </div>
      <button
        @click="resetFilters"
        class="text-xs text-gray-500 hover:text-red-600 font-medium flex items-center space-x-1 transition-colors"
      >
        <span>✕</span>
        <span>Réinitialiser</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useHistoriqueStore } from '~/stores/historique'

const store = useHistoriqueStore()

const filterDay = ref('')
const filterMonth = ref('')
const filterYear = ref('')

const hasActiveFilter = computed(() => !!store.activeFilter)

const activeFilterLabel = computed(() => {
  if (!store.activeFilter) return ''
  if (store.activeFilter === 'jour') return `Jour : ${filterDay.value}`
  if (store.activeFilter === 'mois') return `Mois : ${filterMonth.value}`
  if (store.activeFilter === 'annee') return `Année : ${filterYear.value}`
  return ''
})

const availableYears = computed(() => {
  const years = new Set(store.allTransactions.map(t => new Date(t.date).getFullYear()))
  return Array.from(years).sort((a, b) => b - a)
})

const applyDayFilter = () => {
  if (!filterDay.value) return
  filterMonth.value = ''
  filterYear.value = ''
  store.filterByDay(filterDay.value)
}

const applyMonthFilter = () => {
  if (!filterMonth.value) return
  filterDay.value = ''
  filterYear.value = ''
  const [year, month] = filterMonth.value.split('-')
  store.filterByMonth(Number(year), Number(month))
}

const applyYearFilter = () => {
  if (!filterYear.value) {
    resetFilters()
    return
  }
  filterDay.value = ''
  filterMonth.value = ''
  store.filterByYear(Number(filterYear.value))
}

const resetFilters = () => {
  filterDay.value = ''
  filterMonth.value = ''
  filterYear.value = ''
  store.resetFilters()
}
</script>
