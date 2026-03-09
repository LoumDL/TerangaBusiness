<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-6">
      <span class="mr-2">💳</span>
      Payer avec PayDunya
    </h2>

    <!-- Redirection en cours -->
    <Transition name="slide-down">
      <div v-if="redirecting" class="flex flex-col items-center justify-center py-10 space-y-4">
        <div class="w-12 h-12 border-4 border-navy-200 border-t-navy-500 rounded-full animate-spin" />
        <p class="text-sm font-medium text-gray-700">Redirection vers PayDunya…</p>
        <p class="text-xs text-gray-400">Vous allez être redirigé vers la page de paiement sécurisée.</p>
      </div>
    </Transition>

    <!-- Erreur -->
    <div v-if="store.error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
      ⚠️ {{ store.error }}
    </div>

    <form v-if="!redirecting" @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Description -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Description <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.description"
          type="text"
          placeholder="Ex: Cotisation mois d'août 2026"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-navy-400 focus:border-transparent outline-none transition-all"
          :class="errors.description ? 'border-red-400 bg-red-50' : ''"
        />
        <p v-if="errors.description" class="mt-1 text-xs text-red-600">{{ errors.description }}</p>
      </div>

      <!-- Montant -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Montant (FCFA) <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input
            v-model.number="form.montant"
            type="number"
            min="1"
            placeholder="20000"
            class="w-full px-4 py-2.5 pr-16 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-navy-400 focus:border-transparent outline-none transition-all"
            :class="errors.montant ? 'border-red-400 bg-red-50' : ''"
          />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-400">FCFA</span>
        </div>
        <p v-if="errors.montant" class="mt-1 text-xs text-red-600">{{ errors.montant }}</p>
        <p v-else-if="form.montant > 0" class="mt-1 text-xs text-gray-400">
          {{ formatMontantFCFA(form.montant) }}
        </p>
      </div>

      <!-- Méthode de paiement -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Méthode de paiement <span class="text-red-500">*</span>
        </label>
        <div class="grid grid-cols-3 gap-3">
          <button
            v-for="method in paymentMethods"
            :key="method.value"
            type="button"
            @click="form.channel = method.value"
            class="relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition-all duration-200 cursor-pointer"
            :class="form.channel === method.value
              ? 'border-navy-500 bg-navy-50 shadow-sm'
              : 'border-gray-200 hover:border-gray-300 bg-white'"
          >
            <span class="text-2xl mb-1">{{ method.icon }}</span>
            <span class="text-xs font-medium" :class="form.channel === method.value ? 'text-navy-700' : 'text-gray-600'">
              {{ method.label }}
            </span>
            <!-- Checkmark sélectionné -->
            <div
              v-if="form.channel === method.value"
              class="absolute top-1.5 right-1.5 w-4 h-4 bg-navy-500 rounded-full flex items-center justify-center"
            >
              <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </button>
        </div>
        <p v-if="errors.channel" class="mt-1 text-xs text-red-600">{{ errors.channel }}</p>
      </div>

      <!-- Justificatif optionnel -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Justificatif <span class="text-xs text-gray-400 font-normal">(optionnel)</span>
        </label>
        <FileUpload @update:file="form.justificatif = $event" />
      </div>

      <!-- Résumé avant paiement -->
      <div v-if="form.montant > 0 && form.channel" class="p-3 bg-navy-50 border border-navy-100 rounded-xl text-sm">
        <p class="text-navy-700 font-medium text-xs mb-1">Récapitulatif</p>
        <div class="flex justify-between items-center">
          <span class="text-navy-600 text-xs">{{ selectedMethod?.label }}</span>
          <span class="font-bold text-navy-800">{{ formatMontantFCFA(form.montant) }}</span>
        </div>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="isLoading"
        class="w-full py-3 px-6 bg-teranga-500 hover:bg-teranga-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2"
      >
        <span v-if="isLoading" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
        <span>{{ isLoading ? 'Initialisation…' : `Payer ${form.montant > 0 ? formatMontantFCFA(form.montant) : ''} →` }}</span>
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { usePaiementStore } from '~/stores/paiement'
import { usePaiement } from '~/composables/usePaiement'
import { useFormatCurrency } from '~/composables/useFormatCurrency'

const store = usePaiementStore()
const { initierPaiement } = usePaiement()
const { formatMontantFCFA } = useFormatCurrency()

const redirecting = ref(false)

const paymentMethods = [
  { value: 'wave',   label: 'Wave',         icon: '🌊' },
  { value: 'orange', label: 'Orange Money', icon: '🟠' },
  { value: 'card',   label: 'Carte',        icon: '💳' },
]

const form = reactive({
  description: '',
  montant: 0,
  channel: '' as 'wave' | 'orange' | 'card' | '',
  justificatif: null as File | null,
})

const errors = reactive({
  description: '',
  montant: '',
  channel: '',
})

const isLoading = computed(() => store.isLoading)
const selectedMethod = computed(() => paymentMethods.find(m => m.value === form.channel))

const validate = (): boolean => {
  errors.description = ''
  errors.montant = ''
  errors.channel = ''
  let valid = true

  if (!form.description.trim()) {
    errors.description = 'La description est obligatoire.'
    valid = false
  }
  if (!form.montant || form.montant <= 0) {
    errors.montant = 'Le montant doit être supérieur à 0.'
    valid = false
  }
  if (!form.channel) {
    errors.channel = 'Veuillez choisir une méthode de paiement.'
    valid = false
  }
  return valid
}

const router = useRouter()

const handleSubmit = async () => {
  if (!validate()) return

  try {
    const data = await initierPaiement(
      form.description,
      form.montant,
      form.channel,
      form.justificatif,
    )

    const query = {
      paiement_id: String(data.paiement_id),
      montant: String(form.montant),
      description: form.description,
    }

    // Wave et Orange → page custom dans l'app (saisie numéro + push USSD)
    if (form.channel === 'wave') {
      router.push({ path: '/paiement-wave', query })
      return
    }
    if (form.channel === 'orange') {
      router.push({ path: '/paiement-orange', query })
      return
    }

    // Carte bancaire → redirection PayDunya
    redirecting.value = true
    setTimeout(() => { window.location.href = data.checkout_url }, 1500)
  } catch {
    // error shown from store
  }
}
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
