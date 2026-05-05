<template>
  <div class="app-shell">
    <Navbar :is-dark="isDark" @toggle-theme="toggleTheme" />
    <main class="container">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import Navbar from './components/Navbar.vue'

const isDark = ref(false)

const applyTheme = () => {
  document.documentElement.classList.toggle('dark', isDark.value)
}

const toggleTheme = () => {
  isDark.value = !isDark.value
  localStorage.setItem('study_planner_theme', isDark.value ? 'dark' : 'light')
}

onMounted(() => {
  isDark.value = localStorage.getItem('study_planner_theme') === 'dark'
  applyTheme()
})

watch(isDark, applyTheme)
</script>
