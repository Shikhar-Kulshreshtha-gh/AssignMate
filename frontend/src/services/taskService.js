import api from './api'

const taskService = {
  getTasks(params) {
    return api.get('/tasks', { params })
  },
  createTask(payload) {
    return api.post('/tasks', payload)
  },
  updateTask(id, payload) {
    return api.put(`/tasks/${id}`, payload)
  },
  deleteTask(id) {
    return api.delete(`/tasks/${id}`)
  }
}

export default taskService
