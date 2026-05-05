<template>
  <section class="stats">
    <article><h4>Total</h4><p>{{ stats.total }}</p></article>
    <article><h4>Completed</h4><p>{{ stats.completed }}</p></article>
    <article><h4>Pending</h4><p>{{ stats.pending }}</p></article>
    <article><h4>Overdue</h4><p class="danger">{{ stats.overdue }}</p></article>
  </section>
  <section class="chart">
    <div class="bar-group">
      <span>Completed</span>
      <div class="bar"><div class="fill done" :style="{ width: `${completionPct}%` }"></div></div>
      <strong>{{ completionPct }}%</strong>
    </div>
    <div class="bar-group">
      <span>Pending</span>
      <div class="bar"><div class="fill pending" :style="{ width: `${pendingPct}%` }"></div></div>
      <strong>{{ pendingPct }}%</strong>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  stats: {
    type: Object,
    required: true
  }
})

const completionPct = computed(() => {
  if (!props.stats.total) return 0
  return Math.round((props.stats.completed / props.stats.total) * 100)
})

const pendingPct = computed(() => {
  if (!props.stats.total) return 0
  return Math.round((props.stats.pending / props.stats.total) * 100)
})
</script>
