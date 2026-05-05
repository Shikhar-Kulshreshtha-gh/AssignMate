<template>
  <header class="topbar">
    <div class="brand">Study Planner</div>
    <div class="actions">
      <button class="btn ghost" @click="$emit('toggle-theme')">
        {{ isDark ? 'Light Mode' : 'Dark Mode' }}
      </button>
    </div>
  </header>
</template>

<script setup>
defineProps({
  isDark: {
    type: Boolean,
    required: true
  }
})
</script>
