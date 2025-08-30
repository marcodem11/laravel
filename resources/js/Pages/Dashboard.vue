<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
const user = usePage().props.auth.user
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Dashboard
      </h2>
    </template>

    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-bold">Ciao, {{ user.name }}!</h1>

      <div class="grid md:grid-cols-2 gap-4">
        <!-- User area -->
        <div class="p-4 rounded-2xl shadow border">
          <h2 class="font-semibold mb-2">Le mie richieste</h2>
          <p class="text-sm text-gray-600 mb-3">Crea e gestisci le tue richieste.</p>
          <div class="flex gap-2">
            <Link href="/requests/create" class="px-3 py-2 rounded bg-black text-white">Nuova richiesta</Link>
            <Link href="/requests" class="px-3 py-2 rounded border">Le mie richieste</Link>
          </div>
        </div>

        <!-- Admin area -->
        <div v-if="user.role === 'admin'" class="p-4 rounded-2xl shadow border">
          <h2 class="font-semibold mb-2">Area Admin</h2>
          <p class="text-sm text-gray-600 mb-3">Inventario e approvazioni utenti.</p>
          <div class="flex gap-2">
            <Link href="/admin/items" class="px-3 py-2 rounded border">Inventario</Link>
            <Link href="/admin/requests" class="px-3 py-2 rounded bg-black text-white">Richieste utenti</Link>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>