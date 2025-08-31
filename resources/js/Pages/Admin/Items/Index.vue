<script setup>
import { reactive, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({ items: Object, categories: Array })

/* ---- CREATE ---- */
const form = useForm({
  name: '', category_id: '', quantity: 1, status: 'available'
})
const submit = () => form.post('/admin/items', { preserveScroll: true })

/* ---- EDIT INLINE ---- */
const editId = ref(null)
const edit = reactive({
  name: '',
  category_id: '',
  quantity: 1,
  status: 'available',
})

function startEdit(it) {
  editId.value = it.id
  edit.name = it.name
  edit.category_id = it.category_id
  edit.quantity = it.quantity
  edit.status = it.status
}

function cancelEdit() {
  editId.value = null
}

function saveEdit() {
  const f = useForm({
    name: edit.name,
    category_id: edit.category_id,
    quantity: edit.quantity,
    status: edit.status,
    _method: 'put',
  })
  f.post(`/admin/items/${editId.value}`, {
    preserveScroll: true,
    onSuccess: () => { editId.value = null },
  })
}

/* ---- DELETE ---- */
const destroyItem = (id) => {
  if (!confirm('Eliminare questo item?')) return
  router.delete(`/admin/items/${id}`, { preserveScroll: true })
}
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
          <!-- Nome -->
          <td class="p-2">
            <template v-if="editId === it.id">
              <input v-model="edit.name" class="border px-2 py-1 w-full" />
            </template>
            <template v-else>{{ it.name }}</template>
          </td>

          <!-- Categoria -->
          <td class="p-2 text-center">
            <template v-if="editId === it.id">
              <select v-model="edit.category_id" class="border px-2 py-1">
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </template>
            <template v-else>{{ it.category?.name }}</template>
          </td>

          <!-- Qty -->
          <td class="p-2 text-center">
            <template v-if="editId === it.id">
              <input type="number" min="0" v-model.number="edit.quantity" class="border px-2 py-1 w-20 text-center" />
            </template>
            <template v-else>{{ it.quantity }}</template>
          </td>

          <!-- Stato -->
          <td class="p-2 text-center">
            <template v-if="editId === it.id">
              <select v-model="edit.status" class="border px-2 py-1">
                <option value="available">available</option>
                <option value="unavailable">unavailable</option>
              </select>
            </template>
            <template v-else>
              <span
                :class="[
                  'px-2 py-1 rounded text-xs',
                  it.status === 'available' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700'
                ]"
              >
                {{ it.status }}
              </span>
            </template>
          </td>

          <!-- Azioni -->
          <td class="p-2 text-right space-x-2">
            <template v-if="editId === it.id">
              <button @click="saveEdit" class="px-2 py-1 border rounded">Salva</button>
              <button @click="cancelEdit" class="px-2 py-1 border rounded">Annulla</button>
            </template>
            <template v-else>
              <button @click="startEdit(it)" class="px-2 py-1 border rounded">Modifica</button>
              <button @click="destroyItem(it.id)" class="px-2 py-1 border rounded text-red-600">Elimina</button>
            </template>
          </td>
        </tr>

        <tr v-if="props.items.data.length === 0">
          <td colspan="5" class="p-4 text-center text-gray-500">Nessun item</td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination (semplice) -->
    <div class="flex gap-2" v-if="props.items.links">
      <a
        v-for="l in props.items.links"
        :key="l.url"
        :href="l.url || '#'"
        :class="['px-2 py-1 border', { 'font-bold': l.active, 'opacity-50 pointer-events-none': !l.url }]"
        v-html="l.label"
      />
    </div>
  </div>
</template>