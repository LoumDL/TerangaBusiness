import { usePaiementStore } from '~/stores/paiement'
import { useHistoriqueStore } from '~/stores/historique'
import { useAuthStore } from '~/stores/auth'
import type { PaiementResponse, Transaction } from '~/types'

export const usePaiement = () => {
  const store = usePaiementStore()
  const historiqueStore = useHistoriqueStore()
  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  const initierPaiement = async (
    description: string,
    montant: number,
    justificatif: File
  ): Promise<PaiementResponse> => {
    store.setLoading(true)
    store.error = null

    try {
      const formData = new FormData()
      formData.append('description', description)
      formData.append('montant', String(montant))
      formData.append('justificatif', justificatif)

      const response = await fetch(`${config.public.apiBase}/api/v1/paiements`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json',
        },
        body: formData,
      })

      if (!response.ok) {
        const error = await response.json().catch(() => ({ message: 'Erreur inconnue' }))
        throw new Error(error.message || `Erreur ${response.status}`)
      }

      const data = (await response.json()) as PaiementResponse
      store.setResult(data.paiement, data.message)

      // Ajouter à l'historique local
      const newTransaction: Transaction = {
        id: data.paiement.id,
        type: 'PAIEMENT',
        description: data.paiement.description,
        montant: data.paiement.montant,
        statut: data.paiement.statut,
        date: data.paiement.created_at,
      }
      historiqueStore.allTransactions = [newTransaction, ...historiqueStore.allTransactions]
      if (!historiqueStore.activeFilter) {
        historiqueStore.filteredTransactions = [newTransaction, ...historiqueStore.filteredTransactions]
      }

      return data
    } catch (err) {
      store.error = err instanceof Error ? err.message : 'Erreur lors du paiement.'
      throw err
    } finally {
      store.setLoading(false)
    }
  }

  return { initierPaiement }
}
