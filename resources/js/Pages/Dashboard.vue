<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  isAdmin: Boolean,
  myKpi: Object,
  adminKpi: Object,
  catalog: Object,      // solo user
  categories: Array,    // solo user
  filters: Object,      // solo user
})

const user = usePage().props.auth.user

const filters = computed(() => props.filters || { q:'', category_id:null, status:'available' })
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
    </template>

    <div class="p-6 space-y-8">
      <h1 class="text-2xl font-bold">Ciao, {{ user.name }}!</h1>

      <!-- KPI comuni -->
      <div class="grid gap-4 sm:grid-cols-2">
        <div class="p-4 rounded-2xl shadow border">
          <div class="text-sm text-gray-500">Le mie richieste in attesa</div>
          <div class="text-3xl font-bold">{{ props.myKpi?.pending ?? 0 }}</div>
        </div>
        <div class="p-4 rounded-2xl shadow border">
          <div class="text-sm text-gray-500">Le mie richieste approvate</div>
          <div class="text-3xl font-bold">{{ props.myKpi?.approved ?? 0 }}</div>
        </div>
      </div>

      <!-- Navigazione user -->
      <div class="grid md:grid-cols-2 gap-4">
        <div class="p-4 rounded-2xl shadow border">
          <h3 class="font-semibold mb-2">Le mie richieste</h3>
          <p class="text-sm text-gray-600 mb-3">Crea e gestisci le tue richieste.</p>
          <div class="flex gap-2">
            <Link href="/requests/create" class="px-3 py-2 rounded bg-black text-white">Nuova richiesta</Link>
            <Link href="/requests" class="px-3 py-2 rounded border">Le mie richieste</Link>
            <Link href="/reservations" class="px-3 py-2 rounded border">Le mie prenotazioni</Link>
          </div>
        </div>

        <!-- Area Admin -->
        <div v-if="props.isAdmin" class="p-4 rounded-2xl shadow border">
          <h3 class="font-semibold mb-2">Area Admin</h3>
          <p class="text-sm text-gray-600 mb-3">Inventario e approvazioni utenti.</p>
          <div class="flex gap-2">
            <Link href="/admin/items" class="px-3 py-2 rounded border">Inventario</Link>
            <Link href="/admin/requests" class="px-3 py-2 rounded bg-black text-white">Richieste utenti</Link>
          </div>
        </div>
      </div>

      <!-- 📦 Catalogo (solo user) -->
      <div v-if="!props.isAdmin" class="space-y-4">
        <h2 class="text-xl font-semibold">Catalogo</h2>

        <!-- Filtri -->
        <form method="GET" action="/dashboard" class="flex flex-wrap gap-2">
          <input
            type="text" name="q" :value="filters.q"
            placeholder="Cerca per nome..."
            class="rounded border px-3 py-2"
          />
          <select name="category_id" :value="filters.category_id || ''" class="rounded border px-3 py-2">
            <option value="">Tutte le categorie</option>
            <option v-for="c in props.categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <select name="status" :value="filters.status" class="rounded border px-3 py-2">
            <option value="available">Disponibili</option>
            <option value="unavailable">Non disponibili</option>
          </select>
          <button class="px-3 py-2 rounded bg-black text-white">Filtra</button>
        </form>

        <!-- Lista -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm border">
            <thead class="bg-gray-50">
              <tr>
                <th class="p-2 text-left">Nome</th>
                <th class="p-2">Categoria</th>
                <th class="p-2">Qty</th>
                <th class="p-2">Stato</th>
                <th class="p-2"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="it in props.catalog?.data || []" :key="it.id" class="border-t">
                <td class="p-2">{{ it.name }}</td>
                <td class="p-2">{{ it.category?.name ?? '-' }}</td>
                <td class="p-2 text-center">{{ it.quantity }}</td>
                <td class="p-2 text-center">
                  <span :class="['px-2 py-1 rounded text-xs', it.status==='available' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700']">
                    {{ it.status }}
                  </span>
                </td>
                <td class="p-2 text-right">
                  <Link :href="`/requests/create?item_id=${it.id}`" class="px-2 py-1 rounded border">Richiedi</Link>
                </td>
              </tr>
              <tr v-if="!props.catalog || props.catalog.data?.length === 0">
                <td colspan="5" class="p-4 text-center text-gray-500">Nessun item trovato</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginazione -->
        <div class="mt-3 flex gap-2" v-if="props.catalog?.links">
          <a v-for="l in props.catalog.links" :key="l.url" :href="l.url || '#'"
             :class="['px-2 py-1 border rounded', { 'font-bold': l.active, 'opacity-50 pointer-events-none': !l.url }]"
             v-html="l.label" />
        </div>
      </div>

      <!-- KPI Admin (già presenti sopra) -->
      <div v-if="props.isAdmin" class="space-y-4">
        <h2 class="text-xl font-semibold">Statistiche (ultimi 30 giorni)</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div class="p-4 rounded-2xl shadow border">
            <div class="text-sm text-gray-500">Items totali</div>
            <div class="text-3xl font-bold">{{ props.adminKpi?.totalItems ?? 0 }}</div>
          </div>
          <div class="p-4 rounded-2xl shadow border">
            <div class="text-sm text-gray-500">Richieste in attesa</div>
            <div class="text-3xl font-bold">{{ props.adminKpi?.pendingRequests ?? 0 }}</div>
          </div>
          <div class="p-4 rounded-2xl shadow border">
            <div class="text-sm text-gray-500">Richieste approvate (mese)</div>
            <div class="text-3xl font-bold">{{ props.adminKpi?.approvedThisMonth ?? 0 }}</div>
          </div>
          <div class="p-4 rounded-2xl shadow border">
            <div class="text-sm text-gray-500">Prenotazioni attive oggi</div>
            <div class="text-3xl font-bold">{{ props.adminKpi?.activeToday ?? 0 }}</div>
          </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
          <div class="p-4 rounded-2xl shadow border">
            <div class="text-sm text-gray-500 mb-1">Item più richiesto (qty)</div>
            <div v-if="props.adminKpi?.topItem" class="text-lg font-semibold">
              {{ props.adminKpi.topItem.name }} — {{ props.adminKpi.topItem.qty }}
              <div class="text-xs text-gray-500">{{ props.adminKpi.topItem.range }}</div>
            </div>
            <div v-else class="text-gray-500">Nessun dato</div>
          </div>

          <div class="p-4 rounded-2xl shadow border">
            <div class="text-sm text-gray-500 mb-1">Utente più attivo (richieste)</div>
            <div v-if="props.adminKpi?.topUser" class="text-lg font-semibold">
              {{ props.adminKpi.topUser.name }} — {{ props.adminKpi.topUser.count }}
              <div class="text-xs text-gray-500">{{ props.adminKpi.topUser.email }}</div>
              <div class="text-xs text-gray-500">{{ props.adminKpi.topUser.range }}</div>
            </div>
            <div v-else class="text-gray-500">Nessun dato</div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>