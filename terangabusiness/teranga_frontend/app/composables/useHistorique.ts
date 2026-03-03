import { useHistoriqueStore } from '~/stores/historique'
import { useApi } from '~/utils/api'
import type { HistoriqueData } from '~/types'

export const useHistorique = () => {
  const store = useHistoriqueStore()
  const { apiFetch } = useApi()

  const fetchHistorique = async (): Promise<void> => {
    store.isLoading = true
    store.error = null
    try {
      const data = await apiFetch<HistoriqueData>('/v1/historique')
      store.setTransactions(data.transactions)
    } catch (err) {
      store.error = err instanceof Error ? err.message : 'Impossible de charger l\'historique.'
    } finally {
      store.isLoading = false
    }
  }

  const deleteTransaction = async (id: number): Promise<void> => {
    await apiFetch(`/v1/historique/${id}`, { method: 'DELETE' })
    store.removeTransaction(id)
  }

  return { fetchHistorique, deleteTransaction }
}
