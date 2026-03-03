<template>
  <div class="min-h-screen bg-gray-50">
    <AppHeader />

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page title -->
      <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
          <NuxtLink to="/dashboard" class="hover:text-navy-500 transition-colors">Accueil</NuxtLink>
          <span>›</span>
          <span class="text-gray-900 font-medium">Paiement</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Initier un Paiement</h1>
        <p class="text-sm text-gray-500 mt-1">Soumettez votre cotisation avec un justificatif</p>
      </div>

      <!-- Info banner -->
      <div class="bg-navy-50 border border-navy-200 rounded-xl p-4 mb-6 flex items-start space-x-3">
        <span class="text-lg flex-shrink-0">ℹ️</span>
        <div class="text-sm text-navy-700">
          <p class="font-semibold">Cotisation mensuelle : 20 000 FCFA</p>
          <p class="mt-0.5 text-navy-600 text-xs">
            Votre paiement sera validé automatiquement. Joignez obligatoirement un justificatif (reçu, capture d'écran, etc.)
          </p>
        </div>
      </div>

      <PaiementForm />

      <!-- Recent paiements link -->
      <div class="mt-4 text-center">
        <NuxtLink
          to="/historique"
          class="text-sm text-navy-500 hover:text-navy-600 font-medium transition-colors"
        >
          Voir l'historique de mes paiements →
        </NuxtLink>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })
useHead({ title: 'Paiement' })
</script>
