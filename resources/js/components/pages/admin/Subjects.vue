<template>
  <div class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">Manage Subjects</h1>
      </div>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h5 class="fw-semibold mb-3">Add New Subject</h5>
          <div v-if="error" class="alert alert-danger">{{ error }}</div>
          <div v-if="success" class="alert alert-success">{{ success }}</div>
          <form @submit.prevent="createSubject" class="row g-3">
            <div class="col-md-5">
              <label class="form-label">Name</label>
              <input v-model="form.name" type="text" class="form-control" required placeholder="e.g., Physics">
            </div>
            <div class="col-md-7">
              <label class="form-label">Description</label>
              <input v-model="form.description" type="text" class="form-control" placeholder="Short description (optional)">
            </div>
            <div class="col-12 d-flex gap-2">
              <button class="btn btn-primary" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Save Subject
              </button>
              <button type="button" class="btn btn-outline-secondary" @click="reset">Reset</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Slug</th>
                  <th>Description</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(s, i) in subjects" :key="s.id">
                  <td>{{ i + 1 }}</td>
                  <td>{{ s.name }}</td>
                  <td><code>{{ s.slug }}</code></td>
                  <td class="text-muted small">{{ s.description }}</td>
                  <td class="text-muted small">{{ new Date(s.created_at).toLocaleString() }}</td>
                </tr>
                <tr v-if="!subjects.length">
                  <td colspan="5" class="text-center text-muted py-4">No subjects yet.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'AdminSubjects',
  data(){
    return {
      subjects: [],
      form: { name: '', description: '' },
      loading: false,
      error: '',
      success: ''
    }
  },
  async created(){
    await this.fetchSubjects()
  },
  methods: {
    async fetchSubjects(){
      try {
        const { data } = await axios.get('/api/admin/subjects')
        this.subjects = data
      } catch (e) {
        this.error = 'Failed to load subjects'
      }
    },
    reset(){ this.form = { name: '', description: '' }; this.error=''; this.success='' },
    async createSubject(){
      this.error=''; this.success=''; this.loading = true
      try {
        const { data } = await axios.post('/api/admin/subjects', this.form)
        this.subjects.unshift(data)
        this.success = 'Subject created'
        this.reset()
      } catch (e) {
        this.error = e?.response?.data?.message || Object.values(e?.response?.data?.errors||{})[0]?.[0] || 'Failed to create subject'
      } finally { this.loading = false }
    }
  }
}
</script>
