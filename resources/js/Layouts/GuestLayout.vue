<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen bg-neutral-900 text-neutral-100 flex flex-col items-center pt-8 sm:justify-center sm:pt-0">
    <div>
      <Link href="/" class="inline-flex items-center justify-center rounded-xl bg-neutral-800/60 p-3 border border-neutral-700">
        <ApplicationLogo class="h-8 w-8 fill-current text-neutral-200" />
      </Link>
    </div>

    <div class="mt-6 w-full overflow-hidden bg-neutral-950/70 backdrop-blur px-6 py-6 shadow-xl sm:max-w-md sm:rounded-2xl border border-neutral-800">
      <slot />
    </div>

    <!-- Subtle footer -->
    <div class="mt-6 text-xs text-neutral-500">
      Inventory App • Dark mode
    </div>
  </div>
</template>