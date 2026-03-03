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
            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
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
            <td class="px-6 py-4 text-center">
              <button
                @click="confirmDelete(t)"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                title="Supprimer"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
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
          <div class="ml-3 flex items-center space-x-2 flex-shrink-0">
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-900">{{ formatMontantFCFA(t.montant) }}</p>
              <CotisationBadge :statut="t.statut" class="mt-1" />
            </div>
            <button
              @click="confirmDelete(t)"
              class="ml-2 inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
              title="Supprimer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="itemToDelete"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="itemToDelete = null" />

          <!-- Modal -->
          <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 z-10">
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900 text-center mb-1">Supprimer la transaction</h3>
            <p class="text-sm text-gray-500 text-center mb-1">{{ itemToDelete.description }}</p>
            <p class="text-base font-semibold text-gray-900 text-center mb-5">{{ formatMontantFCFA(itemToDelete.montant) }}</p>

            <p class="text-sm text-gray-400 text-center mb-6">Cette action est irréversible.</p>

            <div class="flex space-x-3">
              <button
                @click="itemToDelete = null"
                class="flex-1 py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all text-sm"
              >
                Annuler
              </button>
              <button
                @click="handleDelete"
                :disabled="isDeleting"
                class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 disabled:bg-red-300 text-white font-semibold rounded-xl transition-all text-sm flex items-center justify-center"
              >
                <span v-if="isDeleting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2" />
                {{ isDeleting ? 'Suppression…' : 'Supprimer' }}
              </button>
            </div>

            <p v-if="deleteError" class="mt-3 text-xs text-red-600 text-center">{{ deleteError }}</p>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import type { Transaction } from '~/types'
import { useFormatCurrency } from '~/composables/useFormatCurrency'
import { useFormatDate } from '~/composables/useFormatDate'
import { useHistorique } from '~/composables/useHistorique'

defineProps<{
  transactions: Transaction[]
  isLoading: boolean
}>()

const { formatMontantFCFA } = useFormatCurrency()
const { formatDate } = useFormatDate()
const { deleteTransaction } = useHistorique()

const itemToDelete = ref<Transaction | null>(null)
const isDeleting = ref(false)
const deleteError = ref<string | null>(null)

const confirmDelete = (t: Transaction) => {
  deleteError.value = null
  itemToDelete.value = t
}

const handleDelete = async () => {
  if (!itemToDelete.value) return
  isDeleting.value = true
  deleteError.value = null
  try {
    await deleteTransaction(itemToDelete.value.id)
    itemToDelete.value = null
  } catch {
    deleteError.value = 'Impossible de supprimer. Réessayez.'
  } finally {
    isDeleting.value = false
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
