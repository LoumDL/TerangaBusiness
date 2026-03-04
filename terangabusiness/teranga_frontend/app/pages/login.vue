<template>
  <div class="min-h-screen bg-gradient-to-br from-navy-600 via-navy-500 to-teranga-600 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo card -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-xl mb-4 overflow-hidden">
          <img src="/logo.jpeg" alt="Teranga Business Hub" class="w-full h-full object-cover" />
        </div>
        <h1 class="text-2xl font-bold text-white">Teranga Business Hub</h1>
        <p class="text-gray-300 text-sm mt-1">Finance Solidaire</p>
      </div>

      <!-- Form card -->
      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-1">Connexion</h2>
        <p class="text-sm text-gray-500 mb-6">Accédez à votre espace membre</p>

        <div v-if="authStore.error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex items-center">
          <span class="mr-2">⚠️</span>{{ authStore.error }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="demo@teranga.sn"
              autocomplete="email"
              class="w-full px-4 py-2.5 border rounded-xl text-sm outline-none transition-all focus:ring-2 focus:ring-navy-400 focus:border-transparent"
              :class="errors.email ? 'border-red-400 bg-red-50' : 'border-gray-300'"
            />
            <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                autocomplete="current-password"
                class="w-full px-4 py-2.5 pr-11 border rounded-xl text-sm outline-none transition-all focus:ring-2 focus:ring-navy-400 focus:border-transparent"
                :class="errors.password ? 'border-red-400 bg-red-50' : 'border-gray-300'"
              />
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                @click="showPassword = !showPassword"
              >
                {{ showPassword ? '🙈' : '👁️' }}
              </button>
            </div>
            <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password }}</p>
          </div>

          <button
            type="submit"
            :disabled="authStore.isLoading"
            class="w-full py-3 bg-navy-500 hover:bg-navy-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2"
          >
            <span v-if="authStore.isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
            <span>{{ authStore.isLoading ? 'Connexion…' : 'Se connecter' }}</span>
          </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
          <p class="text-sm text-gray-500">
            Pas encore membre ?
            <NuxtLink to="/register" class="font-semibold text-navy-500 hover:text-navy-600 ml-1">
              Créer un compte
            </NuxtLink>
          </p>
        </div>

        <!-- Demo credentials -->
        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
          <p class="text-xs font-semibold text-amber-700 mb-1">Compte de démo :</p>
          <p class="text-xs text-amber-600 font-mono">demo@teranga.sn / Demo@2026</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import { useAuth } from '~/composables/useAuth'

definePageMeta({ middleware: 'auth' })

useHead({ title: 'Connexion' })

const authStore = useAuthStore()
const { login } = useAuth()

const form = reactive({ email: '', password: '' })
const errors = reactive({ email: '', password: '' })
const showPassword = ref(false)

const validate = (): boolean => {
  errors.email = ''
  errors.password = ''
  let valid = true

  if (!form.email) {
    errors.email = 'L\'email est obligatoire.'
    valid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'L\'email n\'est pas valide.'
    valid = false
  }

  if (!form.password) {
    errors.password = 'Le mot de passe est obligatoire.'
    valid = false
  }

  return valid
}

const handleLogin = async () => {
  if (!validate()) return
  authStore.error = null
  try {
    await login(form.email, form.password)
  } catch {
    // error is shown from store
  }
}
</script>
