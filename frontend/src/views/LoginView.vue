<template>
  <div class="auth-card">
    <h2>Login</h2>
    <form @submit.prevent="handleSubmit">
      <label>Email<input v-model.trim="form.email" type="email" required /></label>
      <label>Password<input v-model="form.password" type="password" minlength="8" required /></label>
      <p class="error" v-if="errorMessage">{{ errorMessage }}</p>
      <button class="btn primary" type="submit" :disabled="loading">{{ loading ? 'Signing in...' : 'Login' }}</button>
    </form>
    <router-link to="/register">Create account</router-link>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import authService from '../services/authService'

const router = useRouter()
const loading = ref(false)
const errorMessage = ref('')
const form = reactive({ email: '', password: '' })

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await authService.login(form)
    router.push('/dashboard')
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Login failed.'
  } finally {
    loading.value = false
  }
}
</script>
