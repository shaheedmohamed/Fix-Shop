<template>
  <div>
    <section id="top" class="hero-animated-bg py-5 py-lg-6 position-relative">
      <div class="container py-5">
        <div class="row align-items-center gy-4">
          <div class="col-lg-6" v-reveal>
            <span class="badge bg-gradient-primary text-white mb-3 shadow-sm">Fix & Shop · Digitopia 2025</span>
            <img src="/images/logo-fix & shop.png" alt="Fix & Shop" class="hero-logo mb-2" />
            <h1 class="display-5 fw-bold text-gradient">AI that teaches you your way</h1>
            <p class="lead text-muted mt-3">Personalized lessons, an always-on AI tutor, and smart reminders.</p>
            <div class="d-flex gap-3 mt-4">
              <RouterLink :to="primaryCta.to" class="btn btn-primary btn-lg px-4 btn-shine">
                <i class="fa-solid fa-user-plus me-2"></i>{{ primaryCta.label }}
              </RouterLink>
              <button type="button" class="btn btn-outline-primary btn-lg px-4" @click="openVideo">
                <i class="fa-solid fa-circle-play me-2"></i>Learn More
              </button>
            </div>
          </div>
          <div class="col-lg-6" v-reveal>
            <div class="ratio ratio-16x9 rounded-4 shadow overflow-hidden bg-white" data-parallax data-speed="0.15">
              <img src="/images/hero2.jpg" alt="Fix & Shop Hero" class="w-100 h-100 animate-float" style="object-fit: cover;" />
            </div>
          </div>
        </div>
      </div>
      <!-- Decorative parallax blobs -->
      <div class="blob blob-primary animate-float" style="width:180px;height:180px; left:-40px; top:40px;" data-parallax data-speed="0.25"></div>
      <div class="blob blob-secondary animate-rotate-slow" style="width:220px;height:220px; right:-60px; top:120px;" data-parallax data-speed="0.18" data-rotate="1"></div>
      <div class="blob blob-accent animate-wobble" style="width:140px;height:140px; left:20%; bottom:-40px;" data-parallax data-speed="0.3"></div>
    </section>

    <!-- Shop Section: Filters + Products -->
    <section class="py-4 bg-white border-top">
      <div class="container">
        <div class="row g-4">
          <!-- Sidebar Filters -->
          <div class="col-lg-3">
            <ShopFilters :categories="categories" :loading-cats="loadingCats" @change="onFiltersChange" />
          </div>
          <!-- Products Grid -->
          <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0">Products</h5>
              <small class="text-muted" v-if="!loadingProducts">{{ products.length }} results</small>
            </div>
            <div v-if="loadingProducts" class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div v-else class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-3">
              <div class="col" v-for="p in products" :key="p.id">
                <ProductCard :product="p" />
              </div>
            </div>
            <div v-if="!loadingProducts && products.length===0" class="alert alert-light border mt-3">No products found.</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Video Overlay -->
    <div v-if="showVideo" class="video-overlay" @click.self="closeVideo">
      <div class="video-card">
        <header class="p-2 text-end">
          <button type="button" class="btn-close" aria-label="Close" @click="closeVideo"></button>
        </header>
        <video src="/video/about.mp4" controls autoplay playsinline></video>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script>
import axios from 'axios'
import Footer from '../layouts/Footer.vue'
import auth from '../../store/auth'
import ProductCard from '../shop/ProductCard.vue'
import ShopFilters from '../shop/Filters.vue'
export default {
  name: 'LandingPage',
  components: { Footer, ProductCard, ShopFilters },
  data(){
    return {
      showVideo: false,
      // shop state
      categories: [],
      loadingCats: false,
      products: [],
      loadingProducts: false,
      filters: { category_id: null, min: null, max: null }
    }
  },
  computed: {
    isLoggedIn(){ return auth.isAuthenticated() },
    stage(){ return auth.state.user?.education_stage || '' },
    primaryCta(){
      return this.isLoggedIn
        ? { to: { name:'subjects' }, label: 'Browse Subjects' }
        : { to: { name:'register' }, label: 'Sign Up' }
    },
  },
  methods: {
    openVideo(){ this.showVideo = true; document.body.classList.add('overflow-hidden') },
    closeVideo(){ this.showVideo = false; document.body.classList.remove('overflow-hidden') },
    onKey(e){ if (e.key === 'Escape') this.closeVideo() },
    async fetchCategories(){
      this.loadingCats = true
      try{
        const { data } = await axios.get('/api/catalog/categories')
        this.categories = data || []
      } finally { this.loadingCats = false }
    },
    async fetchProducts(){
      this.loadingProducts = true
      try{
        const params = {}
        if (this.filters.category_id) params.category = this.filters.category_id
        if (this.filters.min) params.min = this.filters.min
        if (this.filters.max) params.max = this.filters.max
        const { data } = await axios.get('/api/products', { params })
        this.products = data?.data || data || []
      } finally { this.loadingProducts = false }
    },
    onFiltersChange(f){ this.filters = { ...this.filters, ...f }; this.fetchProducts() }
  },
  mounted(){ window.addEventListener('keydown', this.onKey); this.fetchCategories(); this.fetchProducts() },
  beforeUnmount(){ window.removeEventListener('keydown', this.onKey) }
}
</script>

<style scoped>
  .video-overlay{ position: fixed; inset: 0; background: rgba(2,6,23,.72); backdrop-filter: blur(4px); z-index: 1055; display:flex; align-items:center; justify-content:center; padding: 1rem; }
  .video-card{ width: min(960px, 96vw); background: #000; border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,.5); overflow: hidden; position: relative; }
  .video-card header{ position:absolute; top:.5rem; right:.5rem; z-index:2 }
  .video-card button.btn-close{ filter: invert(1); opacity:.85 }
  .video-card video{ width:100%; height:auto; display:block; background:#000 }
</style>
