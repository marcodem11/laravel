<script setup>
import { useForm } from '@inertiajs/vue3'
const props = defineProps({ items: Array })

const form = useForm({
  type: 'inventory',
  item_id: '',
  quantity: 1,
  start_date: '',
  end_date: '',
  note: ''
})
</script>

<template>
  <div class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">Nuova richiesta</h1>

    <div>
      <label class="mr-4"><input type="radio" value="inventory" v-model="form.type"> Inventario</label>
      <label><input type="radio" value="to-buy" v-model="form.type"> Da acquistare</label>
    </div>

    <form @submit.prevent="form.post('/requests')" class="space-y-3">
      <div v-if="form.type==='inventory'" class="flex gap-2 items-end">
        <select v-model="form.item_id" class="border p-2">
          <option value="" disabled>Seleziona item</option>
          <option v-for="i in props.items" :key="i.id" :value="i.id">
            {{ i.name }} ({{ i.category?.name }}) — stock: {{ i.quantity }}
          </option>
        </select>
        <input type="date" v-model="form.start_date" class="border p-2" />
        <input type="date" v-model="form.end_date" class="border p-2" />
        <input type="number" min="1" v-model.number="form.quantity" class="border p-2 w-24" />
      </div>

      <div v-else class="space-y-2">
        <textarea v-model="form.note" class="border p-2 w-full" rows="3"
                  placeholder="Descrivi cosa serve acquistare..."></textarea>
        <input type="number" min="1" v-model.number="form.quantity" class="border p-2 w-24" />
      </div>

      <button class="px-3 py-2 bg-black text-white">Invia</button>
    </form>
  </div>
</template>