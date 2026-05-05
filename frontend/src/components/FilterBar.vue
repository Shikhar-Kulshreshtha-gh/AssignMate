<template>
  <div class="filters">
    <input
      :value="filters.search"
      @input="$emit('update:search', $event.target.value)"
      type="text"
      placeholder="Search title, subject, description..."
    />

    <select :value="filters.status" @change="$emit('update:status', $event.target.value)">
      <option value="">All Status</option>
      <option value="pending">Pending</option>
      <option value="completed">Completed</option>
    </select>

    <input
      :value="filters.subject"
      @input="$emit('update:subject', $event.target.value)"
      type="text"
      placeholder="Filter by subject"
    />
  </div>
</template>

<script setup>
defineProps({
  filters: {
    type: Object,
    required: true
  }
})

defineEmits(['update:search', 'update:status', 'update:subject'])
</script>
