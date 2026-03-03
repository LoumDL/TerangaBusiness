<template>
  <div class="min-h-screen bg-gray-50">
    <AppHeader />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page title -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tableau de Bord</h1>
        <p class="text-sm text-gray-500 mt-1">Bienvenue, {{ authStore.user?.name }} 👋</p>
      </div>

      <!-- Loading skeleton -->
      <AppSkeleton v-if="store.isLoading && store.cotisations.length === 0" />

      <div v-else class="space-y-6">
        <!-- Stats row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <SoldeCard
            :solde="store.soldeGlobal"
            :count="store.cotisations.filter(c => c.statut === 'VALIDÉ').length"
            :is-loading="store.isLoading"
          />

          <!-- Stat card: total cotisations -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Cotisations</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ store.cotisationsCount }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <span class="text-xl">📅</span>
              </div>
            </div>
          </div>

          <!-- Stat card: quick action -->
          <div class="bg-gradient-to-br from-teranga-500 to-teranga-600 rounded-2xl p-5 shadow-sm text-white">
            <div class="flex items-center justify-between mb-3">
              <p class="text-xs font-semibold text-white/70 uppercase tracking-wide">Action Rapide</p>
              <span class="text-xl">💳</span>
            </div>
            <p class="text-sm font-medium mb-3">Effectuez votre cotisation mensuelle</p>
            <NuxtLink
              to="/paiement"
              class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-semibold transition-colors"
            >
              Payer maintenant →
            </NuxtLink>
          </div>
        </div>

        <!-- Cotisation list -->
        <CotisationList
          :cotisations="store.cotisations"
          :is-loading="store.isLoading"
        />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import { useCotisationStore } from '~/stores/cotisation'
import { useCotisation } from '~/composables/useCotisation'

definePageMeta({ middleware: 'auth' })
useHead({ title: 'Tableau de bord' })

const authStore = useAuthStore()
const store = useCotisationStore()
const { fetchDashboard } = useCotisation()

onMounted(async () => {
  await fetchDashboard()
})
</script>
