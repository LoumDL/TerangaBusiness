<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">

      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-3 overflow-hidden">
          <img src="/logo.jpeg" alt="Teranga Business Hub" class="w-full h-full object-cover" />
        </div>
        <p class="text-sm text-gray-500">Teranga Business Hub</p>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center space-y-4">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
          <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900">Paiement annulé</h2>
        <p class="text-sm text-gray-500">
          Vous avez annulé le paiement. Aucun montant n'a été débité.
        </p>
        <div class="pt-4 space-y-2">
          <NuxtLink
            to="/paiement"
            class="block w-full py-3 bg-navy-500 hover:bg-navy-600 text-white font-semibold rounded-xl transition-all text-sm"
          >
            Réessayer le paiement
          </NuxtLink>
          <NuxtLink
            to="/dashboard"
            class="block w-full py-2.5 text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors"
          >
            Retour au tableau de bord
          </NuxtLink>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })
useHead({ title: 'Paiement annulé' })
</script>
