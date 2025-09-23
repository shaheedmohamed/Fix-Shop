<template>
  <div class="py-5">
    <section class="container" v-reveal>
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h4 fw-bold mb-3"><i class="fa-solid fa-people-roof me-2"></i>Parent Dashboard</h1>
              <p class="text-muted mb-4">Add your children to your account and monitor their learning.</p>

              <div class="row g-4">
                <div class="col-md-5">
                  <div class="border rounded-3 p-3">
                    <h5 class="fw-semibold mb-3">Add Child</h5>
                    <div v-if="error" class="alert alert-danger">{{ error }}</div>
                    <div v-if="success" class="alert alert-success">{{ success }}</div>
                    <form @submit.prevent="addChild">
                      <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input v-model="form.name" type="text" class="form-control" placeholder="Child name" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Email (optional)</label>
                        <input v-model="form.email" type="email" class="form-control" placeholder="child@example.com">
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Age (optional)</label>
                        <input v-model.number="form.age" type="number" class="form-control" min="3" max="20" placeholder="Age">
                      </div>
                      <button class="btn btn-primary w-100" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                        Add Child
                      </button>
                    </form>
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="border rounded-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <h5 class="fw-semibold mb-0">Your Children</h5>
                      <button class="btn btn-sm btn-outline-secondary" @click="fetchChildren" :disabled="refreshing">
                        <i class="fa-solid fa-rotate me-1"></i>Refresh
                      </button>
                    </div>
                    <div v-if="children.length===0" class="text-muted small">No children added yet.</div>
                    <div v-else class="list-group">
                      <div class="list-group-item d-flex align-items-center justify-content-between" v-for="c in children" :key="c.id">
                        <div>
                          <div class="fw-semibold">{{ c.name }} <span class="text-muted">(ID: {{ c.id }})</span></div>
                          <div class="small text-muted">Email: {{ c.email || '—' }} • Age: {{ c.age || '—' }}</div>
                          <div v-if="c.child_user_id" class="small text-success">Linked account: #{{ c.child_user_id }}</div>
                        </div>
                        <div class="d-flex gap-2">
                          <RouterLink :to="{ name: 'messages', query:{ child:c.id } }" class="btn btn-sm btn-light">
                            <i class="fa-regular fa-message"></i>
                          </RouterLink>
                          <button class="btn btn-sm btn-outline-danger" @click="removeChild(c.id)" :disabled="removingId===c.id">
                            <span v-if="removingId===c.id" class="spinner-border spinner-border-sm me-1"></span>
                            Remove
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h2 class="h5 fw-bold mb-3"><i class="fa-solid fa-chart-line me-2"></i>Progress Overview (coming soon)</h2>
              <p class="text-muted mb-0">You will see each child's subjects, assignments, and progress here.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'ParentDashboard',
  data(){
    return {
      form: { name:'', email:'', age:null },
      children: [],
      loading: false,
      refreshing: false,
      removingId: null,
      error: '',
      success: ''
    }
  },
  methods:{
    async fetchChildren(){
      this.refreshing = true
      try{
        const { data } = await axios.get('/api/parent/children')
        this.children = data
      } finally {
        this.refreshing = false
      }
    },
    async addChild(){
      this.error=''; this.success=''; this.loading=true
      try{
        const payload = { name: this.form.name, email: this.form.email || undefined, age: this.form.age || undefined }
        const { data } = await axios.post('/api/parent/children', payload)
        this.success = 'Child added'
        this.form = { name:'', email:'', age:null }
        this.children.unshift(data)
      }catch(err){
        this.error = err?.response?.data?.message
          || Object.values(err?.response?.data?.errors || {})[0]?.[0]
          || 'Failed to add child'
      }finally{
        this.loading = false
      }
    },
    async removeChild(id){
      this.removingId = id
      try{
        await axios.delete(`/api/parent/children/${id}`)
        this.children = this.children.filter(c => c.id !== id)
      } finally{
        this.removingId = null
      }
    }
  },
  mounted(){ this.fetchChildren() }
}
</script>
