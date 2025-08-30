<script setup>
const props = defineProps({ reservations: Object })
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Le mie prenotazioni</h1>

    <table class="w-full border text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-2 text-left">Item</th>
          <th class="p-2">Periodo</th>
          <th class="p-2">Qty</th>
          <th class="p-2">Richiesta</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in props.reservations.data" :key="r.id" class="border-t">
          <td class="p-2">{{ r.item?.name }}</td>
          <td class="p-2">{{ r.start_date }} → {{ r.end_date }}</td>
          <td class="p-2">{{ r.quantity }}</td>
          <td class="p-2">#{{ r.item_request_id }}</td>
        </tr>
      </tbody>
    </table>

    <div class="mt-3 flex gap-2" v-if="props.reservations.links">
      <a v-for="l in props.reservations.links" :key="l.url" :href="l.url || '#'"
         :class="['px-2 py-1 border', { 'font-bold': l.active, 'opacity-50 pointer-events-none': !l.url }]"
         v-html="l.label" />
    </div>
  </div>
</template>