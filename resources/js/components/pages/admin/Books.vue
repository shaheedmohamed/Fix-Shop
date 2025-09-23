<template>
  <div class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">Manage Books</h1>
      </div>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h5 class="fw-semibold mb-3">Add New Book</h5>
          <div v-if="error" class="alert alert-danger">{{ error }}</div>
          <div v-if="success" class="alert alert-success">{{ success }}</div>
          <form @submit.prevent="createBook" class="row g-3" enctype="multipart/form-data">
            <div class="col-md-6">
              <label class="form-label">Title</label>
              <input v-model="form.title" type="text" class="form-control" required placeholder="e.g., Algebra Basics">
            </div>
            <div class="col-md-6">
              <label class="form-label">Author</label>
              <input v-model="form.author" type="text" class="form-control" placeholder="e.g., John Doe">
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea v-model="form.description" rows="2" class="form-control" placeholder="Short description (optional)"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Subject</label>
              <select v-model.number="form.subject_id" class="form-select" required>
                <option value="" disabled>Select subject</option>
                <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">PDF File</label>
              <input @change="onFile" type="file" accept="application/pdf" class="form-control" required>
            </div>
            <div class="col-12 d-flex gap-2">
              <button class="btn btn-primary" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Save Book
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
                  <th>Title</th>
                  <th>Author</th>
                  <th>Subject</th>
                  <th>File</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(b, i) in books" :key="b.id">
                  <td>{{ i + 1 }}</td>
                  <td>{{ b.title }}</td>
                  <td class="text-muted small">{{ b.author }}</td>
                  <td><span class="badge bg-secondary">{{ b.subject?.name }}</span></td>
                  <td>
                    <a :href="b.file_url" target="_blank" class="btn btn-sm btn-outline-primary">View/Download</a>
                  </td>
                  <td class="text-muted small">{{ new Date(b.created_at).toLocaleString() }}</td>
                </tr>
                <tr v-if="!books.length">
                  <td colspan="6" class="text-center text-muted py-4">No books yet.</td>
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
  name: 'AdminBooks',
  data(){
    return {
      subjects: [],
      books: [],
      form: { title: '', author: '', description: '', subject_id: '', file: null },
      loading: false,
      error: '',
      success: ''
    }
  },
  async created(){
    await Promise.all([this.fetchSubjects(), this.fetchBooks()])
  },
  methods: {
    async fetchSubjects(){
      try {
        const { data } = await axios.get('/api/admin/subjects')
        this.subjects = data
      } catch (_) { this.error = 'Failed to load subjects' }
    },
    async fetchBooks(){
      try {
        const { data } = await axios.get('/api/admin/books')
        this.books = data?.data || data
      } catch (_) { this.error = 'Failed to load books' }
    },
    reset(){ this.form = { title:'', author:'', description:'', subject_id:'', file:null }; this.error=''; this.success='' },
    onFile(e){ this.form.file = e.target.files[0] },
    async createBook(){
      this.error=''; this.success=''; this.loading=true
      try {
        const fd = new FormData()
        fd.append('title', this.form.title)
        if (this.form.author) fd.append('author', this.form.author)
        if (this.form.description) fd.append('description', this.form.description)
        fd.append('subject_id', this.form.subject_id)
        fd.append('file', this.form.file)
        const { data } = await axios.post('/api/admin/books', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
        this.books.unshift(data)
        this.success = 'Book uploaded'
        this.reset()
      } catch (e) {
        this.error = e?.response?.data?.message || Object.values(e?.response?.data?.errors||{})[0]?.[0] || 'Failed to upload book'
      } finally { this.loading=false }
    }
  }
}
</script>
