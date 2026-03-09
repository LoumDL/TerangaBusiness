import { defineStore } from 'pinia'
import type { Paiement, StatutEnum } from '~/types'

export const usePaiementStore = defineStore('paiement', {
  state: () => ({
    isLoading: false,
    error: null as string | null,
    // Paiement en attente de confirmation PayDunya
    pendingPaiementId: null as number | null,
    checkoutUrl: null as string | null,
    // Résultat final après retour PayDunya
    lastResult: null as { statut: StatutEnum; paiement_id: number } | null,
  }),

  actions: {
    setLoading(val: boolean) {
      this.isLoading = val
    },
    setPending(paiementId: number, checkoutUrl: string) {
      this.pendingPaiementId = paiementId
      this.checkoutUrl = checkoutUrl
      this.error = null
    },
    setResult(statut: StatutEnum, paiementId: number) {
      this.lastResult = { statut, paiement_id: paiementId }
      this.pendingPaiementId = null
      this.checkoutUrl = null
    },
    clearResult() {
      this.lastResult = null
      this.pendingPaiementId = null
      this.checkoutUrl = null
      this.error = null
    },
  },
})
