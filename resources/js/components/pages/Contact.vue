<template>
  <div class="py-5">
    <section class="container">
      <div class="text-center mb-4" v-reveal>
        <h1 class="fw-bold">Contact Us</h1>
        <p class="text-muted">We'd love to hear from you</p>
      </div>

      <div class="row g-4">
        <div class="col-lg-6" v-reveal>
          <form @submit.prevent="submit" class="p-4 bg-white rounded-4 shadow-sm">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input v-model="form.name" type="text" class="form-control form-control-lg" placeholder="Your name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input v-model="form.email" type="email" class="form-control form-control-lg" placeholder="you@example.com" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea v-model="form.message" rows="5" class="form-control" placeholder="How can we help?" required></textarea>
            </div>
            <button class="btn btn-primary btn-lg btn-shine" type="submit">
              <i class="fa-solid fa-paper-plane me-2"></i>Send Message
            </button>
          </form>
        </div>
        <div class="col-lg-6" v-reveal>
          <div class="p-4 bg-light rounded-4 h-100">
            <div class="d-flex align-items-center mb-3">
              <div class="fs-3 text-primary me-2"><i class="fa-solid fa-phone"></i></div>
              <div>
                <div class="fw-semibold">Phone</div>
                <div class="text-muted">+123 456 7890</div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <div class="fs-3 text-primary me-2"><i class="fa-solid fa-envelope"></i></div>
              <div>
                <div class="fw-semibold">Email</div>
                <div class="text-muted">hello@smartpath.ai</div>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="fs-3 text-primary me-2"><i class="fa-brands fa-square-instagram"></i></div>
              <div>
                <div class="fw-semibold">Social</div>
                <div class="text-muted">Follow us for updates</div>
              </div>
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
export default {
  name: 'ContactPage',
  components: { Footer },
  data(){
    return { form: { name:'', email:'', message:'' } }
  },
  methods:{
    submit(){
      alert('Thanks! We\'ll get back to you soon.');
      this.form = { name:'', email:'', message:'' };
    }
  }
}
</script>
