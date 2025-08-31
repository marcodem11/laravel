<script setup>
import { ref, watch } from 'vue'

const props = defineProps({ flash: Object })
const showSuccess = ref(false)
const showError   = ref(false)

watch(() => props.flash?.success, (v) => {
  if (!v) return
  showSuccess.value = true
  setTimeout(() => (showSuccess.value = false), 3000)
})
watch(() => props.flash?.error, (v) => {
  if (!v) return
  showError.value = true
  setTimeout(() => (showError.value = false), 4000)
})
</script>

<template>
  <div class="fixed top-4 right-4 space-y-2 z-50">
    <div v-if="showSuccess" class="rounded shadow px-4 py-2 bg-green-600 text-white">
      {{ flash?.success }}
    </div>
    <div v-if="showError" class="rounded shadow px-4 py-2 bg-red-600 text-white">
      {{ flash?.error }}
    </div>
  </div>
</template>