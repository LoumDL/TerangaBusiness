<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">

      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-3 overflow-hidden">
          <img src="/logo.jpeg" alt="Teranga Business Hub" class="w-full h-full object-cover" />
        </div>
        <p class="text-sm text-gray-500">Teranga Business Hub</p>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">

        <!-- Chargement / polling -->
        <div v-if="status === 'loading'" class="space-y-4">
          <div class="w-16 h-16 border-4 border-navy-100 border-t-navy-500 rounded-full animate-spin mx-auto" />
          <h2 class="text-lg font-bold text-gray-900">Vérification du paiement…</h2>
          <p class="text-sm text-gray-500">
            Nous confirmons votre paiement avec PayDunya.
            <br>Cela peut prendre quelques secondes.
          </p>
          <div class="flex justify-center space-x-1 pt-2">
            <span v-for="i in 3" :key="i"
              class="w-2 h-2 rounded-full bg-navy-300 animate-bounce"
              :style="{ animationDelay: `${(i - 1) * 0.2}s` }"
            />
          </div>
        </div>

        <!-- Succès -->
        <div v-else-if="status === 'success'" class="space-y-4">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900">Paiement validé !</h2>
          <p class="text-sm text-gray-500">
            Votre cotisation de <span class="font-semibold text-gray-700">{{ formatMontantFCFA(montant) }}</span>
            a été enregistrée avec succès.
          </p>
          <p class="text-xs text-gray-400 font-mono">Réf: #{{ paiementId }}</p>
          <div class="pt-4 space-y-2">
            <NuxtLink
              to="/dashboard"
              class="block w-full py-3 bg-navy-500 hover:bg-navy-600 text-white font-semibold rounded-xl transition-all text-sm"
            >
              Retour au tableau de bord
            </NuxtLink>
            <NuxtLink
              to="/historique"
              class="block w-full py-2.5 text-sm text-navy-500 hover:text-navy-600 font-medium transition-colors"
            >
              Voir l'historique
            </NuxtLink>
          </div>
        </div>

        <!-- Rejeté -->
        <div v-else-if="status === 'rejected'" class="space-y-4">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900">Paiement refusé</h2>
          <p class="text-sm text-gray-500">
            Votre paiement n'a pas pu être traité. Veuillez réessayer.
          </p>
          <div class="pt-4 space-y-2">
            <NuxtLink
              to="/paiement"
              class="block w-full py-3 bg-teranga-500 hover:bg-teranga-600 text-white font-semibold rounded-xl transition-all text-sm"
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

        <!-- Timeout (toujours EN_ATTENTE après polling) -->
        <div v-else-if="status === 'timeout'" class="space-y-4">
          <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900">Paiement en cours</h2>
          <p class="text-sm text-gray-500">
            Votre paiement est en cours de traitement. Vérifiez votre historique dans quelques minutes.
          </p>
          <div class="pt-4">
            <NuxtLink
              to="/historique"
              class="block w-full py-3 bg-navy-500 hover:bg-navy-600 text-white font-semibold rounded-xl transition-all text-sm"
            >
              Voir l'historique
            </NuxtLink>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { usePaiement } from '~/composables/usePaiement'
import { useFormatCurrency } from '~/composables/useFormatCurrency'

definePageMeta({ middleware: 'auth' })
useHead({ title: 'Confirmation du paiement' })

const route = useRoute()
const { pollStatus } = usePaiement()
const { formatMontantFCFA } = useFormatCurrency()

type PageStatus = 'loading' | 'success' | 'rejected' | 'timeout'

const status = ref<PageStatus>('loading')
const paiementId = ref<number | null>(null)
const montant = ref(20000) // valeur par défaut pour l'affichage

onMounted(async () => {
  const id = Number(route.query.paiement_id)
  const statutQuery = route.query.statut as string | undefined

  if (!id) {
    navigateTo('/paiement')
    return
  }

  paiementId.value = id

  // Si statut déjà fourni par le mock/redirect
  if (statutQuery === 'VALIDÉ') {
    status.value = 'success'
    return
  }
  if (statutQuery === 'REJETÉ') {
    status.value = 'rejected'
    return
  }

  // Sinon on poll l'API jusqu'à confirmation
  const result = await pollStatus(id)

  if (result.statut === 'VALIDÉ') {
    status.value = 'success'
  } else if (result.statut === 'REJETÉ') {
    status.value = 'rejected'
  } else {
    status.value = 'timeout'
  }
})
</script>
