<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-sky-50">
    <!-- Header / Cover -->
    <div class="relative">
      <div class="h-40 md:h-56 bg-gradient-to-r from-indigo-600 via-sky-500 to-cyan-400"></div>
      <div class="absolute inset-0 opacity-[0.06] pointer-events-none select-none"
           style="background-image: radial-gradient(circle at 20% 10%, white 0, transparent 35%), radial-gradient(circle at 80% 50%, white 0, transparent 35%)"></div>
    </div>

    <section class="container mx-auto -mt-16 px-4 pb-10">
      <div class="max-w-5xl mx-auto backdrop-blur bg-white/80 border border-white/60 shadow-2xl rounded-2xl overflow-hidden ring-1 ring-black/5" v-reveal>
        <!-- Title -->
        <div class="p-6 md:p-8 flex items-center justify-between bg-white/60 border-b">
          <div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900">الملف الشخصي</h1>
            <p class="text-gray-500">تحكم في بياناتك وصورتك الشخصية</p>
          </div>
          <RouterLink :to="{ name: 'dashboard' }" class="text-sm text-indigo-700 hover:text-indigo-900 font-medium">الرجوع للوحة التحكم</RouterLink>
        </div>

        <div class="p-6 md:p-8 grid md:grid-cols-3 gap-8">
          <!-- Avatar card -->
          <div class="md:col-span-1">
            <div
              class="group relative flex flex-col items-center text-center rounded-xl border border-gray-100 bg-white/70 p-6 shadow-sm hover:shadow-md transition">
              <div class="relative">
                <img :src="preview || profile.avatar_url || placeholder"
                     class="h-32 w-32 rounded-full object-cover ring-4 ring-white shadow-lg group-hover:scale-[1.02] transition" />
                <label for="avatar"
                       class="absolute -bottom-2 -right-2 inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1 rounded-full cursor-pointer shadow transition">
                  <i class="fa-solid fa-camera"></i>
                  <span>تغيير</span>
                </label>
                <input id="avatar" type="file" accept="image/*" class="hidden" @change="onAvatar" />
              </div>
              <p class="mt-3 text-xs text-gray-500">يفضل صورة مربعة (JPG/PNG)</p>
              <div v-if="preview" class="mt-2">
                <button @click="preview=''; avatar=null" class="text-xs text-gray-500 hover:text-gray-700 underline">إلغاء المعاينة</button>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form class="md:col-span-2 space-y-6" @submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700">الاسم</label>
                <input v-model="form.name" type="text"
                       class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/70 transition"
                       required />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700">البريد الإلكتروني</label>
                <input v-model="form.email" type="email"
                       class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/70 transition"
                       required />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700">العمر</label>
                <input v-model.number="form.age" type="number" min="1" max="120"
                       class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/70 transition" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700">المرحلة الدراسية</label>
                <select v-model="form.stage"
                        class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/70 transition">
                  <option value="">اختر المرحلة</option>
                  <option>Primary</option>
                  <option>Preparatory</option>
                  <option>Secondary</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700">العنوان</label>
                <input v-model="form.address" type="text"
                       class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/70 transition" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700">رقم الهاتف</label>
                <input v-model="form.phone" type="text"
                       class="mt-1 w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/70 transition" />
              </div>
            </div>

            <!-- Reminders settings -->
            <div class="rounded-xl border bg-white/60 p-4">
              <h3 class="font-bold mb-2">التذكيرات الذكية</h3>
              <p class="text-gray-500 text-sm mb-3">فعل التذكيرات للمراجعة بنظام التكرار المتباعد وحدد وقت التنبيه اليومي.</p>
              <div class="flex items-center gap-4">
                <label class="inline-flex items-center gap-2">
                  <input type="checkbox" v-model="form.reminders_enabled" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                  <span>تفعيل التذكيرات</span>
                </label>
                <div class="flex items-center gap-2">
                  <label class="text-sm text-gray-700">الوقت المفضل</label>
                  <input type="time" v-model="form.reminder_time" class="rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/70 transition" />
                </div>
              </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
              <button :disabled="saving"
                      class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold shadow-sm hover:bg-indigo-700 active:bg-indigo-800 disabled:opacity-60 disabled:cursor-not-allowed transition">
                <span v-if="saving" class="animate-pulse">جاري الحفظ…</span>
                <span v-else>حفظ التغييرات</span>
              </button>
              <span v-if="message" :class="messageClass" class="text-sm">{{ message }}</span>
            </div>
          </form>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script>
import Footer from '../layouts/Footer.vue'
import axios from 'axios'

export default {
  name: 'ProfilePage',
  components: { Footer },
  data(){
    return {
      profile: {},
      form: { name:'', email:'', age:'', stage:'', address:'', phone:'', reminders_enabled:false, reminder_time:'' },
      avatar: null,
      preview: '',
      saving: false,
      message: '',
      placeholder: 'https://api.dicebear.com/7.x/initials/svg?seed=User&backgroundType=gradientLinear'
    }
  },
  computed: {
    messageClass(){ return this.message.includes('Saved') ? 'text-green-600' : 'text-red-600' }
  },
  async created(){
    await this.fetch()
  },
  methods: {
    async fetch(){
      try{
        const { data } = await axios.get('/api/profile')
        this.profile = data
        this.form = {
          name: data.name || '',
          email: data.email || '',
          age: data.age || '',
          stage: data.stage || '',
          address: data.address || '',
          phone: data.phone || '',
          reminders_enabled: !!data.reminders_enabled,
          reminder_time: data.reminder_time || ''
        }
      }catch(e){ this.message = 'Failed to load profile' }
    },
    onAvatar(e){
      const f = e.target.files[0]
      this.avatar = f
      if (f) this.preview = URL.createObjectURL(f)
    },
    async save(){
      this.saving = true
      this.message = ''
      try{
        const fd = new FormData()
        Object.entries(this.form).forEach(([k,v]) => { if(v!=='' && v!==null && v!==undefined) fd.append(k, v) })
        if (this.avatar) fd.append('avatar', this.avatar)
        const { data } = await axios.post('/api/profile', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
        this.profile = data
        this.message = 'Saved successfully'
      }catch(e){
        this.message = e?.response?.data?.message || Object.values(e?.response?.data?.errors||{})[0]?.[0] || 'Failed to save'
      } finally { this.saving = false }
    }
  }
}
</script>
