import { defineStore } from 'pinia'
import type { Cotisation } from '~/types'

export const useCotisationStore = defineStore('cotisation', {
  state: () => ({
    cotisations: [] as Cotisation[],
    soldeGlobal: 0,
    cotisationsCount: 0,
    isLoading: false,
    error: null as string | null,
  }),

  actions: {
    setData(cotisations: Cotisation[], soldeGlobal: number, count: number) {
      this.cotisations = cotisations
      this.soldeGlobal = soldeGlobal
      this.cotisationsCount = count
    },
  },
})
