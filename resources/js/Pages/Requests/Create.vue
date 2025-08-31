<script setup>
import { useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  items: Array,
  prefillItemId: Number, // opzionale
})

const form = useForm({
  type: 'inventory',
  item_id: props.prefillItemId ?? '',
  quantity: 1,
  start_date: '',
  end_date: '',
  note: ''
})

const submit = () =>
  form.post('/requests', {
    preserveScroll: true,
    onSuccess: () => form.reset('note'),
  })
</script>

<template>
  <div class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">Nuova richiesta</h1>

    <div class="space-x-4">
      <label>
        <input type="radio" value="inventory" v-model="form.type"> Inventario
      </label>
      <label>
        <input type="radio" value="to-buy" v-model="form.type"> Da acquistare
      </label>
    </div>

    <form @submit.prevent="submit" class="space-y-4">
      <!-- INVENTARIO -->
      <div v-if="form.type==='inventory'" class="grid gap-3 md:grid-cols-4">
        <div>
          <select v-model="form.item_id" class="border p-2 w-full">
            <option value="" disabled>Seleziona item</option>
            <option v-for="i in props.items" :key="i.id" :value="i.id">
              {{ i.name }} ({{ i.category?.name }}) — stock: {{ i.quantity }}
            </option>
          </select>
          <InputError :message="form.errors.item_id" />
        </div>

        <div>
          <input type="date" v-model="form.start_date" class="border p-2 w-full" />
          <InputError :message="form.errors.start_date" />
        </div>

        <div>
          <input type="date" v-model="form.end_date" class="border p-2 w-full" />
          <InputError :message="form.errors.end_date" />
        </div>

        <div>
          <input type="number" min="1" v-model.number="form.quantity" class="border p-2 w-full" />
          <InputError :message="form.errors.quantity" />
        </div>
      </div>

      <!-- TO-BUY -->
      <div v-else class="space-y-2">
        <div>
          <textarea v-model="form.note" class="border p-2 w-full" rows="3"
                    placeholder="Descrivi cosa serve acquistare..."></textarea>
          <InputError :message="form.errors.note" />
        </div>
        <div>
          <input type="number" min="1" v-model.number="form.quantity" class="border p-2 w-24" />
          <InputError :message="form.errors.quantity" />
        </div>
      </div>

      <button class="px-3 py-2 bg-black text-white rounded">Invia</button>
    </form>
  </div>
</template>