import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/utils/api'
import type { AuthResponse } from '~/types'

export const useAuth = () => {
  const authStore = useAuthStore()
  const { apiFetch } = useApi()
  const router = useRouter()

  const login = async (email: string, password: string): Promise<void> => {
    authStore.isLoading = true
    authStore.error = null
    try {
      const data = await apiFetch<AuthResponse>('/v1/auth/login', {
        method: 'POST',
        body: { email, password } as unknown as BodyInit,
      })
      authStore.setAuth(data.token, data.user)
      await router.push('/dashboard')
    } catch (err) {
      authStore.error = err instanceof Error ? err.message : 'Erreur de connexion.'
      throw err
    } finally {
      authStore.isLoading = false
    }
  }

  const register = async (name: string, email: string, password: string): Promise<void> => {
    authStore.isLoading = true
    authStore.error = null
    try {
      const data = await apiFetch<AuthResponse>('/v1/auth/register', {
        method: 'POST',
        body: { name, email, password } as unknown as BodyInit,
      })
      authStore.setAuth(data.token, data.user)
      await router.push('/dashboard')
    } catch (err) {
      authStore.error = err instanceof Error ? err.message : 'Erreur lors de l\'inscription.'
      throw err
    } finally {
      authStore.isLoading = false
    }
  }

  const logout = async (): Promise<void> => {
    try {
      await apiFetch('/v1/auth/logout', { method: 'POST' })
    } catch {
      // ignore error during logout
    } finally {
      authStore.clearAuth()
      await router.push('/login')
    }
  }

  return { login, register, logout }
}
