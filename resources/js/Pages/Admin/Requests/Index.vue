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
          <td class="p-2">
            <span v-if="r.start_date">{{ r.start_date }} → {{ r.end_date }}</span>
            <span v-else>—</span>
          </td>
          <td class="p-2">{{ r.quantity }}</td>
          <td class="p-2">{{ r.status }}</td>
          <td class="p-2" v-if="r.status==='pending'">
            <button @click="approve(r.id)" class="px-2 py-1 bg-green-600 text-white">Approva</button>
            <button @click="reject(r.id)"  class="ml-2 px-2 py-1 bg-red-600 text-white">Rifiuta</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="mt-3 flex gap-2" v-if="props.requests.links">
      <a v-for="l in props.requests.links" :key="l.url" :href="l.url || '#'"
         :class="['px-2 py-1 border', { 'font-bold': l.active, 'opacity-50 pointer-events-none': !l.url }]"
         v-html="l.label" />
    </div>
  </div>
</template>