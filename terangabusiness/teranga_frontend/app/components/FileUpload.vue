<template>
  <div>
    <div
      class="relative border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-all duration-200"
      :class="[
        isDragging ? 'border-navy-400 bg-navy-50' : 'border-gray-300 hover:border-navy-400 hover:bg-gray-50',
        selectedFile ? 'border-green-400 bg-green-50' : '',
      ]"
      @dragover.prevent="isDragging = true"
      @dragleave="isDragging = false"
      @drop.prevent="handleDrop"
      @click="triggerInput"
    >
      <input
        ref="fileInput"
        type="file"
        class="hidden"
        accept=".pdf,.jpg,.jpeg,.png"
        @change="handleChange"
      />

      <div v-if="!selectedFile">
        <div class="text-4xl mb-2">📎</div>
        <p class="text-sm font-medium text-gray-700">Glissez votre fichier ici ou cliquez</p>
        <p class="text-xs text-gray-400 mt-1">PDF, JPG ou PNG — max 5 MB</p>
      </div>

      <div v-else class="flex items-center justify-center space-x-3">
        <span class="text-2xl">{{ fileIcon }}</span>
        <div class="text-left min-w-0">
          <p class="text-sm font-semibold text-green-700 truncate max-w-xs">{{ selectedFile.name }}</p>
          <p class="text-xs text-gray-500">{{ fileSize }}</p>
        </div>
        <button
          type="button"
          class="ml-2 text-gray-400 hover:text-red-500 transition-colors"
          @click.stop="clearFile"
        >
          ✕
        </button>
      </div>
    </div>

    <p v-if="error" class="mt-1.5 text-xs text-red-600 flex items-center">
      <span class="mr-1">⚠</span>{{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
  (e: 'update:file', file: File | null): void
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)
const isDragging = ref(false)
const error = ref<string | null>(null)

const MAX_SIZE_MB = 5
const ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/png']

const fileIcon = computed(() => {
  if (!selectedFile.value) return ''
  if (selectedFile.value.type === 'application/pdf') return '📄'
  return '🖼️'
})

const fileSize = computed(() => {
  if (!selectedFile.value) return ''
  const kb = selectedFile.value.size / 1024
  return kb > 1024 ? `${(kb / 1024).toFixed(1)} MB` : `${kb.toFixed(0)} KB`
})

const validateFile = (file: File): boolean => {
  error.value = null
  if (!ALLOWED_TYPES.includes(file.type)) {
    error.value = 'Format non supporté. Utilisez PDF, JPG ou PNG.'
    return false
  }
  if (file.size > MAX_SIZE_MB * 1024 * 1024) {
    error.value = `Le fichier dépasse la taille maximale de ${MAX_SIZE_MB} MB.`
    return false
  }
  return true
}

const handleChange = (e: Event) => {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  if (file && validateFile(file)) {
    selectedFile.value = file
    emit('update:file', file)
  }
}

const handleDrop = (e: DragEvent) => {
  isDragging.value = false
  const file = e.dataTransfer?.files?.[0]
  if (file && validateFile(file)) {
    selectedFile.value = file
    emit('update:file', file)
  }
}

const triggerInput = () => {
  fileInput.value?.click()
}

const clearFile = () => {
  selectedFile.value = null
  error.value = null
  emit('update:file', null)
  if (fileInput.value) fileInput.value.value = ''
}
</script>
