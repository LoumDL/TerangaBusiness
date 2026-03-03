import { defineStore } from 'pinia'
import type { User } from '~/types'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: null as string | null,
    isLoading: false,
    error: null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    setAuth(token: string, user: User) {
      this.token = token
      this.user = user
      if (import.meta.client) {
        localStorage.setItem('auth_token', token)
        localStorage.setItem('auth_user', JSON.stringify(user))
      }
    },

    loadFromStorage() {
      if (import.meta.client) {
        const token = localStorage.getItem('auth_token')
        const userStr = localStorage.getItem('auth_user')
        if (token && userStr) {
          this.token = token
          this.user = JSON.parse(userStr) as User
        }
      }
    },

    clearAuth() {
      this.token = null
      this.user = null
      if (import.meta.client) {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
      }
    },
  },
})
