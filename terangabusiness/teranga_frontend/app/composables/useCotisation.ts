import { useCotisationStore } from '~/stores/cotisation'
import { useApi } from '~/utils/api'
import type { DashboardData } from '~/types'

export const useCotisation = () => {
  const store = useCotisationStore()
  const { apiFetch } = useApi()

  const fetchDashboard = async (): Promise<void> => {
    store.isLoading = true
    store.error = null
    try {
      const data = await apiFetch<DashboardData>('/v1/dashboard')
      store.setData(data.cotisations, data.solde_global, data.cotisations_count)
    } catch (err) {
      store.error = err instanceof Error ? err.message : 'Impossible de charger le tableau de bord.'
    } finally {
      store.isLoading = false
    }
  }

  return { fetchDashboard }
}
