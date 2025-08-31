<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, onMounted, onBeforeUnmount } from 'vue'

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

// Polling leggero: ricarica solo 'stats' e (se utente) 'catalog'
const refresh = () => {
  const only = ['myKpi', 'adminKpi']
  if (!props.isAdmin) only.push('catalog')
  router.reload({ only, preserveScroll: true })
}

let t
onMounted(() => { t = setInterval(refresh, 10000) })
onBeforeUnmount(() => clearInterval(t))
</script>

<template>
  <AuthenticatedLayout>

    <div class="p-6 space-y-8">
      <h1 class="text-2xl font-bold">Ciao, {{ user.name }}!</h1>

      <!-- KPI comuni -->
      <div class="grid gap-4 sm:grid-cols-2">
        <div class="card">
          <div class="card-body">
            <div class="text-sm text-gray-500">Le mie richieste in attesa</div>
            <div class="text-3xl font-bold">{{ props.myKpi?.pending ?? 0 }}</div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="text-sm text-gray-500">Le mie richieste approvate</div>
            <div class="text-3xl font-bold">{{ props.myKpi?.approved ?? 0 }}</div>
          </div>
        </div>
      </div>

      <!-- Navigazione user / admin -->
      <div class="grid md:grid-cols-2 gap-4">
        <div class="card">
          <div class="card-body space-y-3">
            <h3 class="card-title">Le mie richieste</h3>
            <p class="text-sm text-gray-600">Crea e gestisci le tue richieste.</p>
            <div class="flex gap-2">
              <Link href="/requests/create" class="btn btn-primary">Nuova richiesta</Link>
              <Link href="/requests" class="btn btn-outline">Le mie richieste</Link>
              <Link href="/reservations" class="btn btn-outline">Le mie prenotazioni</Link>
            </div>
          </div>
        </div>

        <div v-if="props.isAdmin" class="card">
          <div class="card-body space-y-3">
            <h3 class="card-title">Area Admin</h3>
            <p class="text-sm text-gray-600">Inventario e approvazioni utenti.</p>
            <div class="flex gap-2">
              <Link href="/admin/items" class="btn btn-outline">Inventario</Link>
              <Link href="/admin/requests" class="btn btn-primary">Richieste utenti</Link>
            </div>
          </div>
        </div>
      </div>

      <!-- 📦 Catalogo (solo user) -->
      <div v-if="!props.isAdmin" class="space-y-4">
        <h2 class="text-xl font-semibold">Catalogo</h2>

        <form method="GET" action="/dashboard" class="flex flex-wrap gap-2">
          <input type="text" name="q" :value="filters.q" placeholder="Cerca per nome..." class="input"/>
          <select name="category_id" :value="filters.category_id || ''" class="input">
            <option value="">Tutte le categorie</option>
            <option v-for="c in props.categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <select name="status" :value="filters.status" class="input">
            <option value="available">Disponibili</option>
            <option value="unavailable">Non disponibili</option>
          </select>
          <button class="btn btn-primary">Filtra</button>
        </form>

        <div class="overflow-x-auto card">
          <div class="card-body p-0">
            <table class="table-base">
              <thead>
                <tr>
                  <th class="text-left">Nome</th>
                  <th>Categoria</th>
                  <th>Qty</th>
                  <th>Stato</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="it in props.catalog?.data || []" :key="it.id">
                  <td class="text-left">{{ it.name }}</td>
                  <td class="text-center">{{ it.category?.name ?? '-' }}</td>
                  <td class="text-center">{{ it.quantity }}</td>
                  <td class="text-center">
                    <span
                      :class="[
                        'badge',
                        it.status==='available' ? 'badge-success' : 'badge-muted'
                      ]"
                    >{{ it.status }}</span>
                  </td>
                  <td class="text-right">
                    <Link :href="`/requests/create?item_id=${it.id}`" class="btn btn-outline btn-sm">
                      Richiedi
                    </Link>
                  </td>
                </tr>
                <tr v-if="!props.catalog || props.catalog.data?.length === 0">
                  <td colspan="5" class="p-4 text-center text-gray-500">Nessun item trovato</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Paginazione -->
        <div class="mt-3 flex gap-2" v-if="props.catalog?.links">
          <a
            v-for="l in props.catalog.links"
            :key="l.url"
            :href="l.url || '#'"
            :class="['btn btn-outline btn-sm', { 'font-bold': l.active, 'opacity-50 pointer-events-none': !l.url }]"
            v-html="l.label"
          />
        </div>
      </div>

      <!-- KPI Admin -->
      <div v-if="props.isAdmin" class="space-y-4">
        <h2 class="text-xl font-semibold">Statistiche (ultimi 30 giorni)</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div class="card"><div class="card-body">
            <div class="text-sm text-gray-500">Items totali</div>
            <div class="text-3xl font-bold">{{ props.adminKpi?.totalItems ?? 0 }}</div>
          </div></div>
          <div class="card"><div class="card-body">
            <div class="text-sm text-gray-500">Richieste in attesa</div>
            <div class="text-3xl font-bold">{{ props.adminKpi?.pendingRequests ?? 0 }}</div>
          </div></div>
          <div class="card"><div class="card-body">
            <div class="text-sm text-gray-500">Richieste approvate (mese)</div>
            <div class="text-3xl font-bold">{{ props.adminKpi?.approvedThisMonth ?? 0 }}</div>
          </div></div>
          <div class="card"><div class="card-body">
            <div class="text-sm text-gray-500">Prenotazioni attive oggi</div>
            <div class="text-3xl font-bold">{{ props.adminKpi?.activeToday ?? 0 }}</div>
          </div></div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
          <div class="card">
            <div class="card-body">
              <div class="text-sm text-gray-500 mb-1">Item più richiesto (qty)</div>
              <div v-if="props.adminKpi?.topItem" class="text-lg font-semibold">
                {{ props.adminKpi.topItem.name }} — {{ props.adminKpi.topItem.qty }}
                <div class="text-xs text-gray-500">{{ props.adminKpi.topItem.range }}</div>
              </div>
              <div v-else class="text-gray-500">Nessun dato</div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
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
    </div>
  </AuthenticatedLayout>
</template>