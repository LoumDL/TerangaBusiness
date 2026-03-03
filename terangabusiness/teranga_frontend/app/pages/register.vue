<template>
  <div class="min-h-screen bg-gradient-to-br from-navy-600 via-navy-500 to-teranga-600 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-xl mb-4 overflow-hidden">
          <img src="/logo.jpeg" alt="Teranga Business Hub" class="w-full h-full object-cover" />
        </div>
        <h1 class="text-2xl font-bold text-white">Teranga Business Hub</h1>
        <p class="text-gray-300 text-sm mt-1">Finance Solidaire • Tontine Digitale</p>
      </div>

      <!-- Form card -->
      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-1">Inscription</h2>
        <p class="text-sm text-gray-500 mb-6">Rejoignez le fonds solidaire Teranga</p>

        <div v-if="authStore.error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex items-center">
          <span class="mr-2">⚠️</span>{{ authStore.error }}
        </div>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Mamadou Diallo"
              autocomplete="name"
              class="w-full px-4 py-2.5 border rounded-xl text-sm outline-none transition-all focus:ring-2 focus:ring-navy-400 focus:border-transparent"
              :class="errors.name ? 'border-red-400 bg-red-50' : 'border-gray-300'"
            />
            <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="mamadou@example.sn"
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
                placeholder="Min 8 caractères, 1 maj, 1 chiffre"
                autocomplete="new-password"
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
            <div v-else class="mt-1.5 flex space-x-1">
              <div
                v-for="i in 4" :key="i"
                class="h-1 flex-1 rounded-full transition-colors"
                :class="passwordStrength >= i ? strengthColors[passwordStrength - 1] : 'bg-gray-200'"
              />
            </div>
          </div>

          <button
            type="submit"
            :disabled="authStore.isLoading"
            class="w-full py-3 bg-teranga-600 hover:bg-teranga-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2"
          >
            <span v-if="authStore.isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
            <span>{{ authStore.isLoading ? 'Inscription…' : 'Créer mon compte' }}</span>
          </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
          <p class="text-sm text-gray-500">
            Déjà membre ?
            <NuxtLink to="/login" class="font-semibold text-navy-500 hover:text-navy-600 ml-1">
              Se connecter
            </NuxtLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import { useAuth } from '~/composables/useAuth'

definePageMeta({ middleware: 'auth' })
useHead({ title: 'Inscription' })

const authStore = useAuthStore()
const { register } = useAuth()

const form = reactive({ name: '', email: '', password: '' })
const errors = reactive({ name: '', email: '', password: '' })
const showPassword = ref(false)

const passwordStrength = computed(() => {
  const p = form.password
  if (!p) return 0
  let score = 0
  if (p.length >= 8) score++
  if (/[A-Z]/.test(p)) score++
  if (/\d/.test(p)) score++
  if (/[^A-Za-z0-9]/.test(p)) score++
  return score
})

const strengthColors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500']

const validate = (): boolean => {
  errors.name = ''
  errors.email = ''
  errors.password = ''
  let valid = true

  if (!form.name.trim()) {
    errors.name = 'Le nom est obligatoire.'
    valid = false
  }

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
  } else if (form.password.length < 8) {
    errors.password = 'Le mot de passe doit contenir au moins 8 caractères.'
    valid = false
  } else if (!/[A-Z]/.test(form.password) || !/\d/.test(form.password)) {
    errors.password = 'Le mot de passe doit contenir au moins 1 majuscule et 1 chiffre.'
    valid = false
  }

  return valid
}

const handleRegister = async () => {
  if (!validate()) return
  authStore.error = null
  try {
    await register(form.name, form.email, form.password)
  } catch {
    // error from store
  }
}
</script>
