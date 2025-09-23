<template>
  <section class="container py-5">
    <h1 class="h3 mb-3">Admin Logs</h1>
    <p class="text-muted mb-3">Latest application events.</p>

    <div v-if="loading" class="alert alert-info py-2">Loading logs…</div>
    <div v-else-if="error" class="alert alert-danger py-2">{{ error }}</div>

    <ul v-else class="list-group">
      <li v-for="log in logs" :key="log.id" class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <span :class="['badge me-2', levelClass(log.level)]">{{ log.level }}</span>
          <span>{{ log.message }}</span>
        </div>
        <small class="text-muted">{{ new Date(log.time).toLocaleString() }}</small>
      </li>
    </ul>
  </section>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AdminLogsPage',
  data() {
    return { logs: [], loading: true, error: null }
  },
  methods: {
    levelClass(level) {
      if (level === 'error') return 'bg-danger'
      if (level === 'warning') return 'bg-warning text-dark'
      return 'bg-secondary'
    }
  },
  async mounted() {
    try {
      const { data } = await axios.get('/api/admin/logs')
      this.logs = data
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || 'Failed to load logs'
    } finally {
      this.loading = false
    }
  }
}
</script>
