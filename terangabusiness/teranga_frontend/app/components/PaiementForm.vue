<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-6">
      <span class="mr-2">💳</span>
      Initier un Paiement
    </h2>

    <!-- Result banner -->
    <Transition name="slide-down">
      <div v-if="result" class="mb-6 p-4 rounded-xl border" :class="resultClass">
        <div class="flex items-start space-x-3">
          <span class="text-xl flex-shrink-0">{{ resultIcon }}</span>
          <div>
            <p class="font-semibold text-sm">{{ resultTitle }}</p>
            <p class="text-sm mt-0.5 opacity-80">{{ paiementStore.lastMessage }}</p>
            <p class="text-xs mt-1 font-mono opacity-60">
              Référence: {{ result.id }}
            </p>
          </div>
        </div>
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-current/10">
          <div class="flex items-center space-x-2">
            <span class="text-sm font-semibold">{{ formatMontantFCFA(result.montant) }}</span>
            <CotisationBadge :statut="result.statut" />
          </div>
          <button @click="paiementStore.clearResult()" class="text-xs underline opacity-60 hover:opacity-100">
            Nouveau paiement
          </button>
        </div>
      </div>
    </Transition>

    <form v-if="!result" @submit.prevent="handleSubmit" class="space-y-5">
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

      <!-- Justificatif -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
          Justificatif <span class="text-red-500">*</span>
        </label>
        <FileUpload @update:file="form.justificatif = $event" />
        <p v-if="errors.justificatif" class="mt-1 text-xs text-red-600">{{ errors.justificatif }}</p>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="isLoading"
        class="w-full py-3 px-6 bg-navy-500 hover:bg-navy-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2"
      >
        <span v-if="isLoading" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
        <span>{{ isLoading ? 'Traitement en cours…' : 'Envoyer le paiement' }}</span>
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { usePaiementStore } from '~/stores/paiement'
import { usePaiement } from '~/composables/usePaiement'
import { useFormatCurrency } from '~/composables/useFormatCurrency'

const paiementStore = usePaiementStore()
const { initierPaiement } = usePaiement()
const { formatMontantFCFA } = useFormatCurrency()

const form = reactive({
  description: '',
  montant: 0,
  justificatif: null as File | null,
})

const errors = reactive({
  description: '',
  montant: '',
  justificatif: '',
})

const isLoading = computed(() => paiementStore.isLoading)
const result = computed(() => paiementStore.lastResult)

const resultClass = computed(() => {
  if (!result.value) return ''
  return result.value.statut === 'VALIDÉ'
    ? 'bg-green-50 border-green-200 text-green-800'
    : 'bg-red-50 border-red-200 text-red-800'
})

const resultIcon = computed(() => {
  if (!result.value) return ''
  return result.value.statut === 'VALIDÉ' ? '✅' : '❌'
})

const resultTitle = computed(() => {
  if (!result.value) return ''
  return result.value.statut === 'VALIDÉ' ? 'Paiement validé avec succès !' : 'Paiement rejeté'
})

const validate = (): boolean => {
  let valid = true
  errors.description = ''
  errors.montant = ''
  errors.justificatif = ''

  if (!form.description.trim()) {
    errors.description = 'La description est obligatoire.'
    valid = false
  }
  if (!form.montant || form.montant <= 0) {
    errors.montant = 'Le montant doit être supérieur à 0.'
    valid = false
  }
  if (!form.justificatif) {
    errors.justificatif = 'Le justificatif est obligatoire.'
    valid = false
  }
  return valid
}

const handleSubmit = async () => {
  if (!validate()) return

  try {
    await initierPaiement(form.description, form.montant, form.justificatif!)
    form.description = ''
    form.montant = 0
    form.justificatif = null
  } catch {
    // errors are handled in the store
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
