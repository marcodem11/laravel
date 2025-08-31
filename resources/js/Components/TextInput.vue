<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel();
const input = ref(null);

defineExpose({
  focus: () => input.value?.focus(),
});

onMounted(() => {});
</script>

<template>
  <!-- base dark theme via utility .input -->
  <input
    v-model="model"
    ref="input"
    class="input focus:ring-1 focus:ring-neutral-600 focus:border-neutral-500"
  />
</template>