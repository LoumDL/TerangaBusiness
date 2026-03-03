import { useAuthStore } from '~/stores/auth'

export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore()

  // Load token from localStorage on client
  if (import.meta.client) {
    authStore.loadFromStorage()
  }

  const publicRoutes = ['/login', '/register']

  if (!authStore.isAuthenticated && !publicRoutes.includes(to.path)) {
    return navigateTo('/login')
  }

  if (authStore.isAuthenticated && publicRoutes.includes(to.path)) {
    return navigateTo('/dashboard')
  }
})
