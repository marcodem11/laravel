<script setup>
import { router } from '@inertiajs/vue3'
const props = defineProps({ requests: Object })
const approve = (id) => router.post(`/admin/requests/${id}/approve`)
const reject  = (id) => router.post(`/admin/requests/${id}/reject`)
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Richieste utenti</h1>
    <table class="w-full border text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-2 text-left">Utente</th>
          <th class="p-2">Tipo</th>
          <th class="p-2">Item / Note</th>
          <th class="p-2">Periodo</th>
          <th class="p-2">Qty</th>
          <th class="p-2">Stato</th>
          <th class="p-2"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in props.requests.data" :key="r.id" class="border-t">
          <td class="p-2">{{ r.user?.name }}</td>
          <td class="p-2">{{ r.type }}</td>
          <td class="p-2">{{ r.item?.name || r.note }}</td>
          <td class="p-2">{{ r.start_date ?? '—' }} <span v-if="r.end_date">→ {{ r.end_date }}</span></td>
          <td class="p-2">{{ r.quantity }}</td>
          <td class="p-2">{{ r.status }}</td>
          <td class="p-2" v-if="r.status==='pending'">
            <button @click="approve(r.id)" class="px-2 py-1 bg-green-600 text-white">Approva</button>
            <button @click="reject(r.id)"  class="ml-2 px-2 py-1 bg-red-600 text-white">Rifiuta</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>