<template>
  <section class="dashboard-page">
    <div class="dashboard-header">
      <h1>Study Dashboard</h1>
      <button class="btn" @click="logout">Logout</button>
    </div>

    <DashboardStats :stats="stats" />

    <TaskForm :model-value="editingTask" :error-message="formError" @submit="handleSaveTask" @cancel="cancelEdit" />

    <FilterBar
      :filters="filters"
      @update:search="updateFilter('search', $event)"
      @update:status="updateFilter('status', $event)"
      @update:subject="updateFilter('subject', $event)"
    />

    <TaskList :tasks="tasks" @edit="startEdit" @delete="removeTask" @toggle="toggleTaskStatus" />

    <div class="pagination">
      <button class="btn" :disabled="pagination.page <= 1" @click="changePage(pagination.page - 1)">Prev</button>
      <span>Page {{ pagination.page }} of {{ pagination.total_pages || 1 }}</span>
      <button class="btn" :disabled="pagination.page >= pagination.total_pages" @click="changePage(pagination.page + 1)">Next</button>
    </div>
  </section>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import TaskForm from '../components/TaskForm.vue'
import TaskList from '../components/TaskList.vue'
import FilterBar from '../components/FilterBar.vue'
import DashboardStats from '../components/DashboardStats.vue'
import taskService from '../services/taskService'
import authService from '../services/authService'

const router = useRouter()
const tasks = ref([])
const editingTask = ref(null)
const formError = ref('')
const stats = ref({ total: 0, completed: 0, pending: 0, overdue: 0 })
const pagination = ref({ page: 1, limit: 10, total: 0, total_pages: 1 })

const filters = reactive({
  search: '',
  status: '',
  subject: ''
})

let filterTimer = null

const fetchTasks = async () => {
  const params = {
    page: pagination.value.page,
    limit: pagination.value.limit,
    search: filters.search || undefined,
    status: filters.status || undefined,
    subject: filters.subject || undefined
  }

  const response = await taskService.getTasks(params)
  tasks.value = response.data.data
  stats.value = response.data.stats
  pagination.value = response.data.pagination
}

const debounceFetch = () => {
  clearTimeout(filterTimer)
  filterTimer = setTimeout(async () => {
    pagination.value.page = 1
    await fetchTasks()
  }, 250)
}

const updateFilter = (key, value) => {
  filters[key] = value
  debounceFetch()
}

const handleSaveTask = async (payload) => {
  formError.value = ''
  try {
    if (editingTask.value?.id) {
      await taskService.updateTask(editingTask.value.id, payload)
      editingTask.value = null
    } else {
      await taskService.createTask(payload)
    }
    await fetchTasks()
  } catch (error) {
    formError.value = error?.response?.data?.message || 'Unable to save task.'
  }
}

const startEdit = (task) => {
  editingTask.value = { ...task }
}

const cancelEdit = () => {
  editingTask.value = null
  formError.value = ''
}

const removeTask = async (id) => {
  await taskService.deleteTask(id)
  await fetchTasks()
}

const toggleTaskStatus = async (task) => {
  await taskService.updateTask(task.id, {
    ...task,
    status: task.status === 'completed' ? 'pending' : 'completed'
  })
  await fetchTasks()
}

const changePage = async (page) => {
  pagination.value.page = page
  await fetchTasks()
}

const logout = async () => {
  await authService.logout()
  router.push('/login')
}

onMounted(fetchTasks)
</script>
