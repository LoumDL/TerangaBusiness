<template>
  <span
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
    :class="badgeClass"
  >
    <span class="mr-1">{{ badgeIcon }}</span>
    {{ badgeLabel }}
  </span>
</template>

<script setup lang="ts">
import type { StatutEnum } from '~/types'

const props = defineProps<{
  statut: StatutEnum
}>()

const badgeClass = computed(() => {
  switch (props.statut) {
    case 'VALIDÉ':   return 'bg-green-100 text-green-800'
    case 'REJETÉ':   return 'bg-red-100 text-red-800'
    case 'EN_ATTENTE': return 'bg-yellow-100 text-yellow-800'
    default: return 'bg-gray-100 text-gray-800'
  }
})

const badgeIcon = computed(() => {
  switch (props.statut) {
    case 'VALIDÉ':   return '✓'
    case 'REJETÉ':   return '✗'
    case 'EN_ATTENTE': return '⏳'
    default: return '?'
  }
})

const badgeLabel = computed(() => {
  switch (props.statut) {
    case 'VALIDÉ':   return 'Validé'
    case 'REJETÉ':   return 'Rejeté'
    case 'EN_ATTENTE': return 'En attente'
    default: return props.statut
  }
})
</script>
