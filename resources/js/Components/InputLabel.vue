<script setup>
defineProps({
  for: String,
  value: String,
});
</script>

<template>
  <label
    class="block text-sm font-medium text-neutral-300"
    :for="for"
  >
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>