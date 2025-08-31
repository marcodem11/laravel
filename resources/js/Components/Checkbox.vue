<script setup>
const model = defineModel('checked');
</script>

<template>
  <input
    type="checkbox"
    v-model="model"
    class="rounded border-neutral-700 bg-neutral-900 text-neutral-100
           focus:ring-neutral-600 focus:ring-1"
  />
</template>