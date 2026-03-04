<template>
  <header class="bg-navy-500 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo + Brand -->
        <NuxtLink to="/dashboard" class="flex items-center space-x-3 hover:opacity-90 transition-opacity">
          <img src="/logo.jpeg" alt="Teranga Business Hub" class="h-10 w-10 rounded-full object-cover ring-2 ring-white/30" />
          <div class="hidden sm:block">
            <span class="font-bold text-lg leading-tight">TERANGA</span>
            <span class="text-teranga-300 font-semibold text-lg">HUBS</span>
          </div>
        </NuxtLink>

        <!-- Navigation -->
        <nav class="hidden md:flex items-center space-x-1">
          <NuxtLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
            :class="$route.path === link.to
              ? 'bg-white/20 text-white'
              : 'text-gray-300 hover:bg-white/10 hover:text-white'"
          >
            <span class="mr-1.5 text-base">{{ link.icon }}</span>
            {{ link.label }}
          </NuxtLink>
        </nav>

        <!-- User menu -->
        <div class="flex items-center space-x-3">
          <div class="hidden sm:flex items-center space-x-2 text-sm">
            <div class="w-8 h-8 bg-teranga-500 rounded-full flex items-center justify-center text-white font-semibold text-xs">
              {{ userInitials }}
            </div>
            <span class="text-gray-200 font-medium">{{ user?.name }}</span>
          </div>
          <button
            @click="handleLogout"
            class="flex items-center px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors duration-200 text-gray-200 hover:text-white"
          >
            <span class="mr-1">⏻</span>
            <span class="hidden sm:inline">Déconnexion</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile nav -->
    <div class="md:hidden border-t border-white/10">
      <div class="flex overflow-x-auto scrollbar-hide">
        <NuxtLink
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          class="flex-shrink-0 flex flex-col items-center px-4 py-2 text-xs font-medium transition-colors"
          :class="$route.path === link.to
            ? 'bg-white/20 text-white'
            : 'text-gray-300 hover:bg-white/10 hover:text-white'"
        >
          <span class="text-lg mb-0.5">{{ link.icon }}</span>
          {{ link.label }}
        </NuxtLink>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import { useAuth } from '~/composables/useAuth'

const authStore = useAuthStore()
const { logout } = useAuth()

const user = computed(() => authStore.user)
const userInitials = computed(() => {
  if (!user.value?.name) return 'M'
  return user.value.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
})

const navLinks = [
  { to: '/dashboard', icon: '📊', label: 'Tableau de bord' },
  { to: '/paiement', icon: '💳', label: 'Paiement' },
  { to: '/historique', icon: '📋', label: 'Historique' },
]

const handleLogout = async () => {
  await logout()
}
</script>
