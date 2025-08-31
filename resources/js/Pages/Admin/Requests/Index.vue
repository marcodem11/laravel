<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  requests: Object // paginator with data = [{ id, user, type, item, note, start_date, end_date, quantity, status }]
})

const fmt = (d) => {
  if (!d) return '—'
  const date = new Date(d)
  return new Intl.DateTimeFormat('it-IT', { day:'2-digit', month:'2-digit', year:'numeric' }).format(date)
}
const period = (r) => r.type !== 'inventory' ? '—' : `${fmt(r.start_date)} → ${fmt(r.end_date)}`
const statusClass = (s) => ({
  'px-2 py-1 rounded text-xs': true,
  'bg-yellow-100 text-yellow-800': s === 'pending',
  'bg-green-100 text-green-700': s === 'approved',
  'bg-red-100 text-red-700': s === 'rejected'
})

const approve = (id) => useForm({}).post(route('admin.requests.approve', id), { preserveScroll: true })
const reject  = (id) => {
  if (!confirm('Rifiutare questa richiesta?')) return
  useForm({}).post(route('admin.requests.reject', id), { preserveScroll: true })
}

// Polling leggero: ricarica solo 'requests' ogni 10s
const refresh = () => router.reload({ only: ['requests'], preserveScroll: true })
let t
onMounted(() => { t = setInterval(refresh, 10000) })
onBeforeUnmount(() => clearInterval(t))
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Richieste utenti</h1>

    <div class="overflow-x-auto">
      <table class="w-full text-sm border">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">Utente</th>
            <th class="p-2 text-left">Tipo</th>
            <th class="p-2 text-left">Item / Note</th>
            <th class="p-2">Periodo</th>
            <th class="p-2">Qty</th>
            <th class="p-2">Stato</th>
            <th class="p-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in props.requests.data" :key="r.id" class="border-t">
            <td class="p-2">{{ r.user?.name ?? '—' }}</td>
            <td class="p-2">{{ r.type }}</td>
            <td class="p-2">
              <template v-if="r.type === 'inventory'">
                {{ r.item?.name ?? '—' }}
              </template>
              <template v-else>
                <span class="italic text-gray-700">{{ r.note ?? '—' }}</span>
              </template>
            </td>
            <td class="p-2 text-center">{{ period(r) }}</td>
            <td class="p-2 text-center">{{ r.quantity }}</td>
            <td class="p-2 text-center">
              <span :class="statusClass(r.status)">{{ r.status }}</span>
            </td>
            <td class="p-2 text-right space-x-2">
              <template v-if="r.status === 'pending'">
                <button @click="approve(r.id)" class="px-2 py-1 border rounded bg-green-600 text-white">Approva</button>
                <button @click="reject(r.id)" class="px-2 py-1 border rounded text-red-600">Rifiuta</button>
              </template>
            </td>
          </tr>

          <tr v-if="props.requests.data.length === 0">
            <td colspan="7" class="p-4 text-center text-gray-500">Nessuna richiesta</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-3 flex gap-2" v-if="props.requests.links">
      <a
        v-for="l in props.requests.links"
        :key="l.url"
        :href="l.url || '#'"
        :class="['px-2 py-1 border rounded', { 'font-bold': l.active, 'opacity-50 pointer-events-none': !l.url }]"
        v-html="l.label"
      />
    </div>
  </div>
</template>