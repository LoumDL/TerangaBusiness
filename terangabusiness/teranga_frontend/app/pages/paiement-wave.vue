<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">

      <!-- Header Wave -->
      <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-[#1EC8EE] rounded-2xl shadow-lg mb-4">
          <span class="text-4xl">🌊</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Payer avec Wave</h1>
        <p class="text-sm text-gray-500 mt-1">Entrez votre numéro Wave pour recevoir la demande</p>
      </div>

      <!-- Montant -->
      <div class="bg-[#1EC8EE]/10 border border-[#1EC8EE]/30 rounded-2xl p-4 mb-6 text-center">
        <p class="text-xs text-[#0099BB] font-medium mb-1">Montant à payer</p>
        <p class="text-3xl font-bold text-[#007A99]">{{ formatMontantFCFA(montant) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ description }}</p>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

        <!-- Étape 1 : Saisie numéro -->
        <div v-if="step === 'phone'">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Numéro Wave (Sénégal)
          </label>
          <div class="flex items-center border-2 rounded-xl overflow-hidden transition-all"
            :class="error ? 'border-red-400' : 'border-gray-200 focus-within:border-[#1EC8EE]'"
          >
            <span class="px-3 py-3 bg-gray-50 text-sm text-gray-500 border-r border-gray-200 font-medium">+221</span>
            <input
              v-model="phone"
              type="tel"
              maxlength="9"
              placeholder="77 000 00 00"
              class="flex-1 px-3 py-3 text-sm outline-none bg-white"
              @keyup.enter="handleConfirm"
            />
          </div>
          <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
          <p class="mt-1.5 text-xs text-gray-400">Ex: 771234567 ou 781234567</p>

          <button
            @click="handleConfirm"
            :disabled="loading"
            class="mt-5 w-full py-3.5 bg-[#1EC8EE] hover:bg-[#0099BB] disabled:bg-gray-200 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all flex items-center justify-center space-x-2"
          >
            <span v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
            <span>{{ loading ? 'Envoi en cours…' : 'Envoyer la demande Wave' }}</span>
          </button>
        </div>

        <!-- Étape 2 : Attente confirmation téléphone -->
        <div v-else-if="step === 'waiting'" class="text-center space-y-5">
          <div class="relative w-20 h-20 mx-auto">
            <div class="w-20 h-20 rounded-full bg-[#1EC8EE]/20 flex items-center justify-center">
              <span class="text-3xl">📱</span>
            </div>
            <div class="absolute -top-1 -right-1 w-6 h-6 bg-[#1EC8EE] rounded-full flex items-center justify-center">
              <div class="w-3 h-3 bg-white rounded-full animate-ping" />
            </div>
          </div>
          <div>
            <h3 class="font-bold text-gray-900">Demande envoyée !</h3>
            <p class="text-sm text-gray-500 mt-1">
              Vérifiez votre téléphone
              <span class="font-semibold text-gray-700">+221 {{ formatPhone(phone) }}</span>
              et confirmez le paiement sur Wave.
            </p>
          </div>

          <!-- Progress bar -->
          <div class="w-full bg-gray-100 rounded-full h-1.5">
            <div class="bg-[#1EC8EE] h-1.5 rounded-full animate-pulse" style="width: 60%" />
          </div>
          <p class="text-xs text-gray-400">Vérification du paiement… ({{ countdown }}s restantes)</p>

          <button
            @click="step = 'phone'"
            class="text-xs text-gray-400 hover:text-gray-600 underline"
          >
            Changer de numéro
          </button>
        </div>

        <!-- Étape 3 : Résultat -->
        <div v-else-if="step === 'result'" class="text-center space-y-4">
          <div v-if="finalStatus === 'VALIDÉ'" class="space-y-3">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
              <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <h3 class="font-bold text-gray-900 text-lg">Paiement validé !</h3>
            <p class="text-sm text-gray-500">{{ formatMontantFCFA(montant) }} débité depuis Wave.</p>
          </div>
          <div v-else class="space-y-3">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
              <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </div>
            <h3 class="font-bold text-gray-900 text-lg">Paiement non confirmé</h3>
            <p class="text-sm text-gray-500">Vous n'avez pas confirmé à temps ou avez refusé.</p>
            <button @click="step = 'phone'" class="text-sm text-[#1EC8EE] font-medium underline">
              Réessayer
            </button>
          </div>
          <NuxtLink
            to="/dashboard"
            class="block w-full mt-3 py-3 bg-navy-500 hover:bg-navy-600 text-white font-semibold rounded-xl text-sm transition-all"
          >
            Retour au tableau de bord
          </NuxtLink>
        </div>

      </div>

      <!-- Annuler -->
      <div class="text-center mt-4">
        <NuxtLink to="/paiement" class="text-sm text-gray-400 hover:text-gray-600">
          ← Annuler et changer de méthode
        </NuxtLink>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormatCurrency } from '~/composables/useFormatCurrency'
import { useAuthStore } from '~/stores/auth'

definePageMeta({ middleware: 'auth' })
useHead({ title: 'Payer avec Wave' })

const route = useRoute()
const config = useRuntimeConfig()
const authStore = useAuthStore()
const { formatMontantFCFA } = useFormatCurrency()

const paiementId = computed(() => Number(route.query.paiement_id))
const montant    = computed(() => Number(route.query.montant) || 20000)
const description = computed(() => String(route.query.description || 'Cotisation'))

const step        = ref<'phone' | 'waiting' | 'result'>('phone')
const phone       = ref('')
const loading     = ref(false)
const error       = ref('')
const finalStatus = ref<'VALIDÉ' | 'REJETÉ' | 'EN_ATTENTE'>('EN_ATTENTE')
const countdown   = ref(60)

const formatPhone = (p: string) => p.replace(/(\d{2})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4')

const handleConfirm = async () => {
  error.value = ''
  const cleaned = phone.value.replace(/\s/g, '')

  if (!/^[0-9]{9}$/.test(cleaned)) {
    error.value = 'Numéro invalide. Entrez 9 chiffres (ex: 771234567).'
    return
  }

  loading.value = true
  try {
    const response = await fetch(`${config.public.apiBase}/api/v1/paiements/confirm`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${authStore.token}`,
        Accept: 'application/json',
      },
      body: JSON.stringify({
        paiement_id: paiementId.value,
        phone: cleaned,
        channel: 'wave',
      }),
    })

    if (!response.ok) {
      const data = await response.json().catch(() => ({}))
      error.value = data.message || 'Erreur lors de l\'envoi.'
      return
    }

    step.value = 'waiting'
    startPolling()
  } catch {
    error.value = 'Erreur réseau. Vérifiez votre connexion.'
  } finally {
    loading.value = false
  }
}

let pollInterval: ReturnType<typeof setInterval>
let countdownInterval: ReturnType<typeof setInterval>

const startPolling = () => {
  countdown.value = 60

  countdownInterval = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(countdownInterval)
    }
  }, 1000)

  pollInterval = setInterval(async () => {
    try {
      const response = await fetch(`${config.public.apiBase}/api/v1/paiements/${paiementId.value}/status`, {
        headers: { Authorization: `Bearer ${authStore.token}`, Accept: 'application/json' },
      })
      const data = await response.json()

      if (data.statut !== 'EN_ATTENTE') {
        clearInterval(pollInterval)
        clearInterval(countdownInterval)
        finalStatus.value = data.statut
        step.value = 'result'
      }
    } catch {}
  }, 3000)

  // Timeout après 60s
  setTimeout(() => {
    clearInterval(pollInterval)
    clearInterval(countdownInterval)
    if (step.value === 'waiting') {
      finalStatus.value = 'EN_ATTENTE'
      step.value = 'result'
    }
  }, 60000)
}

onUnmounted(() => {
  clearInterval(pollInterval)
  clearInterval(countdownInterval)
})
</script>
