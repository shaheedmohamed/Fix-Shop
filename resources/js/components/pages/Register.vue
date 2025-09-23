<template>
  <div class="py-5">
    <section id="register" class="container" v-reveal>
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
              <h1 class="h3 fw-bold mb-3"><i class="fa-solid fa-user-plus me-2"></i>إنشاء حساب</h1>
              <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>
              <div v-if="success" class="alert alert-success" role="alert">{{ success }}</div>
              <form @submit.prevent="submit">
                <div class="mb-3">
                  <label class="form-label">التسجيل كـ</label>
                  <div class="d-flex gap-3">
                    <label class="form-check">
                      <input class="form-check-input" type="radio" value="buyer" v-model="form.role"> مشترى
                    </label>
                    <label class="form-check">
                      <input class="form-check-input" type="radio" value="provider" v-model="form.role"> مقدم خدمة
                    </label>
                  </div>
                </div>
                <div class="mb-3" v-if="form.role==='buyer'">
                  <label class="form-label">الاسم</label>
                  <input v-model="form.name" type="text" class="form-control form-control-lg" placeholder="اسمك" required>
                </div>
                <div v-else>
                  <div class="mb-3">
                    <label class="form-label">اسم التاجر</label>
                    <input v-model="form.merchant_name" type="text" class="form-control form-control-lg" placeholder="اسم التاجر" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">اسم المتجر</label>
                    <input v-model="form.store_name" type="text" class="form-control form-control-lg" placeholder="اسم المتجر" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">نوع/وصف الخدمة</label>
                    <textarea v-model="form.service_type" rows="2" class="form-control form-control-lg" placeholder="اوصف نوع الخدمة" required></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">لوجو المتجر (اختياري)</label>
                    <input @change="onLogoChange" type="file" accept="image/*" class="form-control form-control-lg">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">الإيميل</label>
                  <input v-model="form.email" type="email" class="form-control form-control-lg" placeholder="you@example.com" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">الهاتف</label>
                  <input v-model="form.phone" type="text" class="form-control form-control-lg" placeholder="(+20) 1X XXX XXXX" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">كلمة المرور</label>
                  <input v-model="form.password" type="password" class="form-control form-control-lg" placeholder="••••••••" required>
                </div>
                <div class="mb-4">
                  <label class="form-label">تأكيد كلمة المرور</label>
                  <input v-model="form.password_confirmation" type="password" class="form-control form-control-lg" placeholder="••••••••" required>
                </div>
                <button class="btn btn-primary btn-lg w-100 btn-shine" type="submit" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  <i class="fa-solid fa-user-check me-2" v-if="!loading"></i>
                  {{ loading ? 'جارى إنشاء الحساب...' : 'تسجيل' }}
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
  name: 'RegisterPage',
  components: { Footer },
  data(){
    return { form: { 
      role:'buyer',
      // buyer
      name:'',
      // provider
      merchant_name:'', store_name:'', service_type:'', logo:null,
      // common
      email:'', phone:'', password:'', password_confirmation:'' }, 
      loading:false, error:'', success:'' }
  },
  methods:{
    onLogoChange(e){
      const f = e.target.files?.[0]
      this.form.logo = f || null
    },
    async submit(){
      this.error=''; this.success='';
      if(this.form.password !== this.form.password_confirmation){ this.error='Passwords do not match'; return }
      this.loading = true
      try{
        const fd = new FormData()
        fd.append('role', this.form.role)
        fd.append('email', this.form.email)
        fd.append('phone', this.form.phone)
        fd.append('password', this.form.password)
        fd.append('password_confirmation', this.form.password_confirmation)
        if(this.form.role === 'buyer'){
          fd.append('name', this.form.name)
        } else {
          fd.append('merchant_name', this.form.merchant_name)
          fd.append('store_name', this.form.store_name)
          fd.append('service_type', this.form.service_type)
          if(this.form.logo){ fd.append('logo', this.form.logo) }
        }

        const { data } = await axios.post('/api/register', fd, { headers: { 'Content-Type':'multipart/form-data' } })
        auth.setToken(data.token)
        auth.setUser(data.user)
        this.success = 'تم إنشاء الحساب بنجاح'
        const dest = data?.redirect || (data.user?.role === 'provider' ? '/dashboard' : '/products')
        setTimeout(()=> this.$router.push(dest), 400)
      }catch(err){
        this.error = err?.response?.data?.message
          || Object.values(err?.response?.data?.errors || {})[0]?.[0]
          || 'فشل إنشاء الحساب'
      }finally{ this.loading = false }
    }
  }
}
</script>

