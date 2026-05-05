<template>
  <section class="task-list">
    <article
      v-for="task in tasks"
      :key="task.id"
      class="task-card"
      :class="[
        `priority-${task.priority}`,
        { completed: task.status === 'completed', overdue: isOverdue(task) }
      ]"
    >
      <div class="task-header">
        <h4>{{ task.title }}</h4>
        <span class="badge">{{ task.priority }}</span>
      </div>
      <p>{{ task.description || 'No description provided.' }}</p>
      <div class="meta">
        <span>{{ task.subject }}</span>
        <span>Due: {{ task.due_date }}</span>
        <span>Status: {{ task.status }}</span>
      </div>
      <div class="actions">
        <button class="btn" @click="$emit('toggle', task)">
          {{ task.status === 'completed' ? 'Mark Pending' : 'Mark Complete' }}
        </button>
        <button class="btn" @click="$emit('edit', task)">Edit</button>
        <button class="btn danger" @click="$emit('delete', task.id)">Delete</button>
      </div>
    </article>
    <p v-if="!tasks.length" class="empty">No tasks found.</p>
  </section>
</template>

<script setup>
defineProps({
  tasks: {
    type: Array,
    default: () => []
  }
})

defineEmits(['edit', 'delete', 'toggle'])

const isOverdue = (task) => {
  if (task.status === 'completed') return false
  return new Date(task.due_date) < new Date(new Date().toISOString().split('T')[0])
}
</script>
