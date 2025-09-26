<template>
  <div>
    <section id="top" class="hero-banner">
      <div class="container">
        <div class="banner-wrap">
          <img src="/images/banar.png" alt="Banner" />
        </div>
      </div>
    </section>

    <!-- Full Products Page embedded on Home -->
    <ProductsPublic />

    <!-- Personalized recommendations like Amazon -->
    <HomeRecommendations />

    <!-- Video Overlay -->
    <div v-if="showVideo" class="video-overlay" @click.self="closeVideo">
      <div class="video-card">
        <header class="p-2 text-end">
          <button type="button" class="btn-close" aria-label="Close" @click="closeVideo"></button>
        </header>
        <video src="/video/about.mp4" controls autoplay playsinline></video>
      </div>
    </div>
  </div>  
</template>

<script>
import axios from 'axios'
import auth from '../../store/auth'
import ProductsPublic from './Products.vue'
import HomeRecommendations from '../sections/HomeRecommendations.vue'
export default {
  name: 'LandingPage',
  components: { ProductsPublic, HomeRecommendations },
  data(){
    return {
      showVideo: false,
      
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
    
  },
  mounted(){ window.addEventListener('keydown', this.onKey) },
  beforeUnmount(){ window.removeEventListener('keydown', this.onKey) }
}
</script>

<style scoped>
  .video-overlay{ position: fixed; inset: 0; background: rgba(2,6,23,.72); backdrop-filter: blur(4px); z-index: 1055; display:flex; align-items:center; justify-content:center; padding: 1rem; }
  .video-card{ width: min(960px, 96vw); background: #000; border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,.5); overflow: hidden; position: relative; }
  .video-card header{ position:absolute; top:.5rem; right:.5rem; z-index:2 }
  .video-card button.btn-close{ filter: invert(1); opacity:.85 }
  .video-card video{ width:100%; height:auto; display:block; background:#000 }

  /* Banner size like example */
  .hero-banner{ padding: 12px 0; background: #fff; }
  .banner-wrap{ height: 600px; border-radius: 16px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,.08); }
  .banner-wrap img{ width: 100%; height: 100%; object-fit: cover; display: block; }
  @media (max-width: 768px){
    .banner-wrap{ height: 260px; border-radius: 12px; }
  }
</style>
