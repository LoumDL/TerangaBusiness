import { defineStore } from 'pinia'
import type { Paiement } from '~/types'

export const usePaiementStore = defineStore('paiement', {
  state: () => ({
    paiements: [] as Paiement[],
    isLoading: false,
    error: null as string | null,
    lastResult: null as Paiement | null,
    lastMessage: null as string | null,
  }),

  actions: {
    setLoading(val: boolean) {
      this.isLoading = val
    },
    setResult(paiement: Paiement, message: string) {
      this.lastResult = paiement
      this.lastMessage = message
    },
    clearResult() {
      this.lastResult = null
      this.lastMessage = null
      this.error = null
    },
  },
})
