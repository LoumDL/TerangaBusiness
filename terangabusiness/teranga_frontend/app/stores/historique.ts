import { defineStore } from 'pinia'
import type { Transaction, FiltreType } from '~/types'

export const useHistoriqueStore = defineStore('historique', {
  state: () => ({
    allTransactions: [] as Transaction[],
    filteredTransactions: [] as Transaction[],
    activeFilter: null as FiltreType,
    filterValue: null as string | null,
    isLoading: false,
    error: null as string | null,
  }),

  actions: {
    setTransactions(transactions: Transaction[]) {
      this.allTransactions = transactions
      this.filteredTransactions = transactions
      this.activeFilter = null
      this.filterValue = null
    },

    filterByDay(date: string) {
      this.activeFilter = 'jour'
      this.filterValue = date
      this.filteredTransactions = this.allTransactions.filter((t) => {
        const d = new Date(t.date)
        const target = new Date(date)
        return (
          d.getFullYear() === target.getFullYear() &&
          d.getMonth() === target.getMonth() &&
          d.getDate() === target.getDate()
        )
      })
    },

    filterByMonth(year: number, month: number) {
      this.activeFilter = 'mois'
      this.filterValue = `${year}-${month}`
      this.filteredTransactions = this.allTransactions.filter((t) => {
        const d = new Date(t.date)
        return d.getFullYear() === year && d.getMonth() + 1 === month
      })
    },

    filterByYear(year: number) {
      this.activeFilter = 'annee'
      this.filterValue = String(year)
      this.filteredTransactions = this.allTransactions.filter((t) => {
        return new Date(t.date).getFullYear() === year
      })
    },

    resetFilters() {
      this.activeFilter = null
      this.filterValue = null
      this.filteredTransactions = this.allTransactions
    },
  },
})
