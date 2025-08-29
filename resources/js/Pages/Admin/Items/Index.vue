<script setup>
import { useForm, router } from '@inertiajs/vue3'
const props = defineProps({ items: Object, categories: Array })

const form = useForm({
  name: '', category_id: '', quantity: 1, status: 'available'
})
const submit = () => form.post('/admin/items')

const update = (it) => {
  const f = useForm({
    name: it.name, category_id: it.category_id, quantity: it.quantity, status: it.status
  })
  f.put(`/admin/items/${it.id}`)
}
const destroyItem = (id) => router.delete(`/admin/items/${id}`)
</script>

<template>
  <div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold">Inventario</h1>

    <!-- Create -->
    <form @submit.prevent="submit" class="flex gap-2 items-end">
      <input v-model="form.name" placeholder="Nome" class="border p-2" />
      <select v-model="form.category_id" class="border p-2">
        <option value="" disabled>Categoria</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <input type="number" min="1" v-model.number="form.quantity" class="border p-2 w-24" />
      <select v-model="form.status" class="border p-2">
        <option value="available">available</option>
        <option value="unavailable">unavailable</option>
      </select>
      <button class="px-3 py-2 bg-black text-white">Aggiungi</button>
    </form>

    <!-- List -->
    <table class="w-full border text-sm">
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
        <tr v-for="it in props.items.data" :key="it.id" class="border-t">
          <td class="p-2">{{ it.name }}</td>
          <td class="p-2">{{ it.category?.name }}</td>
          <td class="p-2">{{ it.quantity }}</td>
          <td class="p-2">{{ it.status }}</td>
          <td class="p-2 flex gap-2">
            <button @click="update(it)" class="px-2 py-1 border">Modifica</button>
            <button @click="destroyItem(it.id)" class="px-2 py-1 border text-red-600">Elimina</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination (semplice) -->
    <div class="flex gap-2" v-if="props.items.links">
      <a v-for="l in props.items.links" :key="l.url" :href="l.url || '#'"
         :class="['px-2 py-1 border', { 'font-bold': l.active, 'opacity-50 pointer-events-none': !l.url }]"
         v-html="l.label" />
    </div>
  </div>
</template>