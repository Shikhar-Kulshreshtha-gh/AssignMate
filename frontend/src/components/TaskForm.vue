<template>
  <form class="task-form" @submit.prevent="handleSubmit">
    <h3>{{ isEdit ? 'Edit Task' : 'Add Task' }}</h3>

    <div class="grid">
      <label>
        Title
        <input v-model.trim="form.title" type="text" maxlength="255" required />
      </label>
      <label>
        Subject
        <input v-model.trim="form.subject" type="text" maxlength="100" required />
      </label>
      <label>
        Priority
        <select v-model="form.priority" required>
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
        </select>
      </label>
      <label>
        Due Date
        <input v-model="form.due_date" type="date" required />
      </label>
      <label v-if="isEdit">
        Status
        <select v-model="form.status" required>
          <option value="pending">Pending</option>
          <option value="completed">Completed</option>
        </select>
      </label>
    </div>

    <label>
      Description
      <textarea v-model.trim="form.description" rows="3" />
    </label>

    <p class="error" v-if="errorMessage">{{ errorMessage }}</p>

    <div class="actions">
      <button class="btn primary" type="submit">{{ isEdit ? 'Update Task' : 'Create Task' }}</button>
      <button class="btn" type="button" v-if="isEdit" @click="$emit('cancel')">Cancel</button>
    </div>
  </form>
</template>

<script setup>
import { computed, reactive, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Object, default: null },
  errorMessage: { type: String, default: '' }
})

const emit = defineEmits(['submit', 'cancel'])

const form = reactive({
  title: '',
  description: '',
  subject: '',
  priority: 'medium',
  due_date: '',
  status: 'pending'
})

const resetForm = (task = null) => {
  form.title = task?.title || ''
  form.description = task?.description || ''
  form.subject = task?.subject || ''
  form.priority = task?.priority || 'medium'
  form.due_date = task?.due_date || ''
  form.status = task?.status || 'pending'
}

watch(
  () => props.modelValue,
  (task) => resetForm(task),
  { immediate: true }
)

const isEdit = computed(() => !!props.modelValue?.id)

const handleSubmit = () => {
  emit('submit', { ...form })
}
</script>
