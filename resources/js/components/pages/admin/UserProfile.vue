<template>
  <section class="container py-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h4 mb-1">User Profile (Admin)</h1>
        <p class="text-muted mb-0">View and edit user's information</p>
      </div>
      <RouterLink class="btn btn-outline-secondary" :to="{ name:'admin-users' }">← Back to Users</RouterLink>
    </div>

    <div v-if="loading" class="alert alert-info">Loading…</div>
    <div v-else-if="error" class="alert alert-danger">{{ error }}</div>

    <div v-else class="row g-4">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center">
            <img :src="preview || data.avatar_url || placeholder" class="rounded-circle shadow" style="width: 140px; height: 140px; object-fit: cover;"/>
            <div class="mt-3">
              <label class="btn btn-sm btn-primary me-2">
                Change Avatar <input type="file" class="d-none" accept="image/*" @change="onAvatar" />
              </label>
              <button class="btn btn-sm btn-outline-secondary" @click="clearAvatar" v-if="preview">Reset</button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form @submit.prevent="save" class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name</label>
                <input v-model="form.name" type="text" class="form-control" required />
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" class="form-control" required />
              </div>
              <div class="col-md-3">
                <label class="form-label">Age</label>
                <input v-model.number="form.age" type="number" min="1" max="120" class="form-control" />
              </div>
              <div class="col-md-3">
                <label class="form-label">Stage</label>
                <select v-model="form.stage" class="form-select">
                  <option value="">Select</option>
                  <option>Primary</option>
                  <option>Preparatory</option>
                  <option>Secondary</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Role</label>
                <select v-model="form.role" class="form-select">
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Address</label>
                <input v-model="form.address" type="text" class="form-control" />
              </div>
              <div class="col-12">
                <label class="form-label">Phone</label>
                <input v-model="form.phone" type="text" class="form-control" />
              </div>
              <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                  Save Changes
                </button>
                <span v-if="message" :class="messageClass">{{ message }}</span>
              </div>
            </form>
          </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
          <div class="card-body">
            <h5 class="fw-semibold">Reset Password</h5>
            <form class="row g-2 align-items-end" @submit.prevent="resetPassword">
              <div class="col-md-4">
                <label class="form-label">New Password</label>
                <input v-model="pwd.password" type="password" class="form-control" required minlength="8" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Confirm Password</label>
                <input v-model="pwd.password_confirmation" type="password" class="form-control" required minlength="8" />
              </div>
              <div class="col-md-4">
                <button class="btn btn-warning">Change Password</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
    <!-- Login History -->
    <div ref="historyCard" class="card border-0 shadow-sm mt-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="fw-semibold mb-0">Login History</h5>
          <button class="btn btn-sm btn-outline-secondary" @click="refreshLogs" :disabled="logsLoading">
            <span v-if="logsLoading" class="spinner-border spinner-border-sm me-2"></span>
            Refresh
          </button>
        </div>
        <div v-if="logsError" class="alert alert-danger py-2">{{ logsError }}</div>
        <div v-else-if="logsLoading" class="text-muted">Loading logs…</div>
        <div v-else>
          <div v-if="logs.length === 0" class="text-muted">No login attempts found.</div>
          <div v-else class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Status</th>
                  <th>Email</th>
                  <th>IP</th>
                  <th>User Agent</th>
                  <th>Time</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(l,i) in logs" :key="i">
                  <td>{{ i+1 }}</td>
                  <td>
                    <span :class="['badge', l.success ? 'bg-success' : 'bg-danger']">{{ l.success ? 'Success' : 'Failed' }}</span>
                  </td>
                  <td>{{ l.email }}</td>
                  <td>{{ l.ip }}</td>
                  <td class="text-truncate" style="max-width: 280px">{{ l.user_agent }}</td>
                  <td>{{ new Date(l.created_at).toLocaleString() }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios'
export default {
  name: 'AdminUserProfile',
  props: { id: { type: [String, Number], required: true } },
  data(){
    return {
      loading: true,
      error: '',
      data: {},
      form: { name:'', email:'', age:'', stage:'', address:'', phone:'', role:'user' },
      avatar: null,
      preview: '',
      saving: false,
      message: '',
      pwd: { password:'', password_confirmation:'' },
      placeholder: 'https://api.dicebear.com/7.x/initials/svg?seed=User&backgroundType=gradientLinear',
      logs: [],
      logsLoading: false,
      logsError: ''
    }
  },
  computed:{
    messageClass(){ return this.message.includes('Saved') ? 'text-success' : 'text-danger' }
  },
  async created(){
    await this.fetch()
    // Auto-load history tab
    if (this.$route?.query?.tab === 'history') {
      await this.loadLogs()
      this.$nextTick(() => { this.$refs.historyCard?.scrollIntoView({ behavior: 'smooth', block: 'start' }) })
    }
  },
  watch: {
    '$route.query.tab'(val){
      if (val === 'history') {
        this.loadLogs().then(()=>{
          this.$nextTick(() => { this.$refs.historyCard?.scrollIntoView({ behavior: 'smooth', block: 'start' }) })
        })
      }
    }
  },
  methods:{
    async fetch(){
      this.loading = true
      try{
        const { data } = await axios.get(`/api/admin/users/${this.id}`)
        this.data = data
        this.form = {
          name: data.name || '',
          email: data.email || '',
          age: data.age || '',
          stage: data.stage || '',
          address: data.address || '',
          phone: data.phone || '',
          role: data.role || 'user'
        }
      }catch(e){ this.error = e?.response?.data?.message || 'Failed to load user' }
      finally{ this.loading = false }
    },
    onAvatar(e){ const f = e.target.files[0]; this.avatar = f; if (f) this.preview = URL.createObjectURL(f) },
    clearAvatar(){ this.avatar=null; this.preview='' },
    async save(){
      this.saving = true
      this.message = ''
      try{
        const fd = new FormData()
        Object.entries(this.form).forEach(([k,v])=>{ if(v!=='' && v!==null && v!==undefined) fd.append(k, v) })
        if (this.avatar) fd.append('avatar', this.avatar)
        const { data } = await axios.post(`/api/admin/users/${this.id}`, fd, { headers:{ 'Content-Type':'multipart/form-data' } })
        this.data = data
        this.message = 'Saved successfully'
      }catch(e){ this.message = e?.response?.data?.message || Object.values(e?.response?.data?.errors||{})[0]?.[0] || 'Failed to save' }
      finally{ this.saving = false }
    },
    async resetPassword(){
      try{
        await axios.post(`/api/admin/users/${this.id}/password`, this.pwd)
        this.pwd = { password:'', password_confirmation:'' }
        alert('Password changed')
      }catch(e){ alert(e?.response?.data?.message || 'Failed to change password') }
    },
    async loadLogs(){
      this.logsLoading = true
      this.logsError = ''
      try{
        const { data } = await axios.get(`/api/admin/users/${this.id}/login-logs`, { params: { limit: 50 } })
        this.logs = data || []
      }catch(e){ this.logsError = e?.response?.data?.message || 'Failed to load login history' }
      finally{ this.logsLoading = false }
    },
    refreshLogs(){ this.loadLogs() }
  }
}
</script>
