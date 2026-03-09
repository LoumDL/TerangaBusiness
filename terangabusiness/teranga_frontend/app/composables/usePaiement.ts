import { usePaiementStore } from '~/stores/paiement'
import { useAuthStore } from '~/stores/auth'
import type { PaiementInitResponse, PaiementStatusResponse } from '~/types'

export const usePaiement = () => {
  const store = usePaiementStore()
  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  const initierPaiement = async (
    description: string,
    montant: number,
    channel: string,
    justificatif?: File | null,
  ): Promise<PaiementInitResponse> => {
    store.setLoading(true)
    store.error = null

    try {
      const formData = new FormData()
      formData.append('description', description)
      formData.append('montant', String(montant))
      formData.append('channel', channel)
      if (justificatif) {
        formData.append('justificatif', justificatif)
      }

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

      const data = await response.json() as PaiementInitResponse
      store.setPending(data.paiement_id, data.checkout_url)
      return data
    } catch (err) {
      store.error = err instanceof Error ? err.message : 'Erreur lors de l\'initialisation du paiement.'
      throw err
    } finally {
      store.setLoading(false)
    }
  }

  const pollStatus = async (
    paiementId: number,
    maxAttempts = 20,
    intervalMs = 3000,
  ): Promise<PaiementStatusResponse> => {
    for (let i = 0; i < maxAttempts; i++) {
      await new Promise(resolve => setTimeout(resolve, intervalMs))

      const response = await fetch(`${config.public.apiBase}/api/v1/paiements/${paiementId}/status`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json',
        },
      })

      if (!response.ok) continue

      const data = await response.json() as PaiementStatusResponse

      if (data.statut !== 'EN_ATTENTE') {
        store.setResult(data.statut, paiementId)
        return data
      }
    }

    // Timeout : retourner EN_ATTENTE après maxAttempts
    const timeout: PaiementStatusResponse = { statut: 'EN_ATTENTE', checkout_url: null }
    store.setResult('EN_ATTENTE', paiementId)
    return timeout
  }

  return { initierPaiement, pollStatus }
}
