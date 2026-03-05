import { useAuthStore } from '~/stores/auth'

export const useApi = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const apiFetch = async <T>(
    endpoint: string,
    options: RequestInit & { method?: string; body?: BodyInit | null | Record<string, unknown> } = {}
  ): Promise<T> => {
    const headers: Record<string, string> = {
      Accept: 'application/json',
    }

    if (authStore.token) {
      headers['Authorization'] = `Bearer ${authStore.token}`
    }

    if (options.body && !(options.body instanceof FormData)) {
      headers['Content-Type'] = 'application/json'
    }

    const response = await fetch(`${config.public.apiBase}/api${endpoint}`, {
      ...options,
      headers: {
        ...headers,
        ...(options.headers as Record<string, string> || {}),
      },
      body: options.body instanceof FormData
        ? options.body
        : options.body
          ? JSON.stringify(options.body)
          : undefined,
    })

    if (!response.ok) {
      const error = await response.json().catch(() => ({ message: `Erreur serveur (${response.status})` }))
      throw new Error(error.message || `Erreur ${response.status}`)
    }

    try {
      return await response.json() as T
    } catch {
      throw new Error('Réponse invalide du serveur. Veuillez réessayer.')
    }
  }

  return { apiFetch }
}
