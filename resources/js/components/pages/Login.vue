<template>
  <div class="py-5">
    <section id="login" class="container" v-reveal>
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h3 fw-bold mb-3"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</h1>
              <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>
              <div v-if="success" class="alert alert-success" role="alert">{{ success }}</div>
              <form @submit.prevent="submit">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input v-model="email" type="email" class="form-control form-control-lg" placeholder="you@example.com" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input v-model="password" type="password" class="form-control form-control-lg" placeholder="••••••••" required>
                </div>
                <button class="btn btn-primary btn-lg w-100 btn-shine" type="submit" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  <i class="fa-solid fa-arrow-right-to-bracket me-2" v-if="!loading"></i>
                  {{ loading ? 'Signing in...' : 'Sign in' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <Footer />
  </div>
</template>

<script>
import Footer from '../layouts/Footer.vue'
import axios from 'axios'
import auth from '../../store/auth'
export default {
  name: 'LoginPage',
  components: { Footer },
  data(){ return { email:'', password:'', loading:false, error:'', success:'' } },
  methods:{
    async submit(){
      this.error=''; this.success=''; this.loading = true
      try{
        const { data } = await axios.post('/api/login', { email:this.email, password:this.password })
        auth.setToken(data.token)
        auth.setUser(data.user)
        this.success = 'Logged in successfully'
        // Redirect to dashboard
        const redirect = this.$route.query.redirect || { name:'dashboard' }
        setTimeout(()=> this.$router.push(redirect), 300)
      }catch(err){
        this.error = err?.response?.data?.message || err?.response?.data?.errors?.email?.[0] || 'Login failed'
      }finally{ this.loading = false }
    }
  }
}
</script>
