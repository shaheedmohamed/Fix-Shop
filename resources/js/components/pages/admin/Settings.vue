<template>
  <section class="container py-5" style="max-width: 720px;">
    <h1 class="h3 mb-3">Admin Settings</h1>
    <p class="text-muted mb-3">Update site-wide settings.</p>

    <div v-if="loading" class="alert alert-info py-2">Loading settings…</div>
    <div v-else>
      <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
      <div v-if="success" class="alert alert-success py-2">Settings saved.</div>

      <form @submit.prevent="save">
        <div class="mb-3">
          <label class="form-label">Site name</label>
          <input v-model="form.site_name" type="text" class="form-control" placeholder="Site name" />
        </div>

        <div class="form-check form-switch mb-3">
          <input v-model="form.maintenance" class="form-check-input" type="checkbox" id="maintenanceSwitch">
          <label class="form-check-label" for="maintenanceSwitch">Maintenance mode</label>
        </div>

        <button class="btn btn-primary" type="submit" :disabled="saving">
          <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
          Save Settings
        </button>
      </form>
    </div>
  </section>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AdminSettingsPage',
  data() {
    return {
      loading: true,
      saving: false,
      error: null,
      success: false,
      form: { site_name: '', maintenance: false },
    }
  },
  async mounted() {
    try {
      const { data } = await axios.get('/api/admin/settings')
      this.form.site_name = data.site_name
      this.form.maintenance = !!data.maintenance
    } catch (e) {
      this.error = e?.response?.data?.message || e.message || 'Failed to load settings'
    } finally {
      this.loading = false
    }
  },
  methods: {
    async save() {
      this.error = null
      this.success = false
      this.saving = true
      try {
        const { data } = await axios.put('/api/admin/settings', this.form)
        this.form.site_name = data.site_name
        this.form.maintenance = !!data.maintenance
        this.success = true
      } catch (e) {
        this.error = e?.response?.data?.message || e.message || 'Failed to save settings'
      } finally {
        this.saving = false
      }
    }
  }
}
</script>
