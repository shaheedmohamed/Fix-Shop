<template>
  <div>
    <section id="top" class="hero-animated-bg py-5 py-lg-6 position-relative">
      <div class="container py-5">
        <div class="row align-items-center gy-4">
          <div class="col-lg-6" v-reveal>
            <span class="badge bg-gradient-primary text-white mb-3 shadow-sm">Smart Path · Digitopia 2025</span>
            <img src="/images/logo-smartpath.png" alt="Smart Path" class="hero-logo mb-2" />
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
              <img src="/images/hero2.jpg" alt="Smart Path Hero" class="w-100 h-100 animate-float" style="object-fit: cover;" />
            </div>
          </div>
        </div>
      </div>
      <!-- Decorative parallax blobs -->
      <div class="blob blob-primary animate-float" style="width:180px;height:180px; left:-40px; top:40px;" data-parallax data-speed="0.25"></div>
      <div class="blob blob-secondary animate-rotate-slow" style="width:220px;height:220px; right:-60px; top:120px;" data-parallax data-speed="0.18" data-rotate="1"></div>
      <div class="blob blob-accent animate-wobble" style="width:140px;height:140px; left:20%; bottom:-40px;" data-parallax data-speed="0.3"></div>
    </section>

    <!-- Icon strip -->
    <section class="py-4 bg-white border-top border-bottom">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 fs-3 text-primary" v-reveal>
          <i class="fa-solid fa-book-open icon-pulse"></i>
          <i class="fa-solid fa-brain"></i>
          <i class="fa-solid fa-robot"></i>
          <i class="fa-solid fa-chart-line"></i>
          <i class="fa-solid fa-bell"></i>
          <i class="fa-solid fa-graduation-cap"></i>
          <i class="fa-solid fa-language"></i>
          <i class="fa-solid fa-microchip"></i>
          <i class="fa-solid fa-lightbulb"></i>
          <i class="fa-solid fa-chalkboard-user"></i>
          <i class="fa-solid fa-trophy"></i>
          <i class="fa-solid fa-star"></i>
        </div>
      </div>
    </section>

    

    <section id="features" class="py-5">
      <div class="container">
        <div class="text-center mb-5" v-reveal>
          <h2 class="fw-bold">Built to learn smarter</h2>
          <p class="text-muted">Personalization, tutoring, reminders, and Q&A — all in one.</p>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3" v-for="(f,i) in features" :key="i" v-reveal>
            <div class="card border-0 shadow-sm h-100 card-hover">
              <div class="card-body p-4">
                <div class="display-6 text-primary mb-3"><i :class="f.icon"></i></div>
                <h5 class="fw-semibold">{{ f.title }}</h5>
                <p class="text-muted mb-0">{{ f.text }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Quick Actions -->
    <section class="py-5 bg-white border-top">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4" v-reveal>
          <h2 class="fw-bold mb-0">Quick Actions</h2>
          <small class="text-muted">Start fast with the most common actions</small>
        </div>
        <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-4">
          <div class="col" v-reveal>
            <RouterLink :to="{ name: 'chat' }" class="text-decoration-none">
              <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                  <div class="display-6 text-primary"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                  <div>
                    <h6 class="fw-semibold mb-1">Build my plan</h6>
                    <p class="text-muted small mb-0">AI creates a weekly study plan</p>
                  </div>
                </div>
              </div>
            </RouterLink>
          </div>
          <div class="col" v-reveal>
            <RouterLink :to="{ name: 'subjects' }" class="text-decoration-none">
              <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                  <div class="display-6 text-success"><i class="fa-solid fa-book-open"></i></div>
                  <div>
                    <h6 class="fw-semibold mb-1">Browse subjects</h6>
                    <p class="text-muted small mb-0">Pick a subject to start</p>
                  </div>
                </div>
              </div>
            </RouterLink>
          </div>
          <div class="col" v-reveal>
            <RouterLink :to="{ name: 'messages' }" class="text-decoration-none">
              <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                  <div class="display-6 text-warning"><i class="fa-regular fa-message"></i></div>
                  <div>
                    <h6 class="fw-semibold mb-1">Message Admin</h6>
                    <p class="text-muted small mb-0">Ask for help or report an issue</p>
                  </div>
                </div>
              </div>
            </RouterLink>
          </div>
          <div class="col" v-reveal>
            <RouterLink :to="{ name: 'chat' }" class="text-decoration-none">
              <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                  <div class="display-6 text-danger"><i class="fa-solid fa-circle-question"></i></div>
                  <div>
                    <h6 class="fw-semibold mb-1">Ask a quick question</h6>
                    <p class="text-muted small mb-0">Chat with the AI tutor instantly</p>
                  </div>
                </div>
              </div>
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Quick FAQs -->
    <section class="py-5 bg-light border-top">
      <div class="container">
        <div class="text-center mb-4" v-reveal>
          <h2 class="fw-bold">Quick FAQs</h2>
          <p class="text-muted mb-0">Answers to common questions to help you get started</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-8 mx-auto">
            <div class="accordion" id="landingFaq">
              <div class="accordion-item" v-for="(q, idx) in faqs" :key="idx" v-reveal>
                <h2 class="accordion-header" :id="'q'+idx">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#a'+idx" aria-expanded="false" :aria-controls="'a'+idx">
                    {{ q.q }}
                  </button>
                </h2>
                <div :id="'a'+idx" class="accordion-collapse collapse" :aria-labelledby="'q'+idx" data-bs-parent="#landingFaq">
                  <div class="accordion-body text-muted">
                    {{ q.a }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="how" class="py-5 bg-light">
      <div class="container">
        <div class="text-center mb-4" v-reveal>
          <h2 class="fw-bold">How it works</h2>
        </div>
        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-5">
          <div class="col" v-for="(s,j) in steps" :key="j" v-reveal>
            <div class="h-100 text-center p-4 bg-white rounded-4 shadow-sm card-hover">
              <div class="fs-1 text-primary mb-2"><i :class="s.icon"></i></div>
              <h6 class="fw-semibold mb-1">{{ s.title }}</h6>
              <p class="text-muted small mb-0">{{ s.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Extra section: Subjects grid based on education stage (visible when logged in) -->
    <section v-if="isLoggedIn && subjects.length" class="py-5">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4" v-reveal>
          <h2 class="fw-bold mb-0">Subjects for {{ stage }}</h2>
          <small class="text-muted">Choose a subject to start learning</small>
        </div>
        <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-3">
          <div class="col" v-for="(sub, idx) in subjects" :key="idx" v-reveal>
            <RouterLink :to="{ name: 'subject', params: { subject: sub.slug } }" class="text-decoration-none">
              <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                  <div class="display-6 text-primary"><i :class="sub.icon"></i></div>
                  <div>
                    <h5 class="fw-semibold mb-1">{{ sub.name }}</h5>
                    <p class="text-muted small mb-0">Click to view {{ sub.name }} lessons</p>
                  </div>
                </div>
              </div>
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Bottom CTA: AI Study Advisor (English, with AI flair) -->
    <section class="py-5 py-lg-6 position-relative bg-gradient" style="--bs-gradient: linear-gradient(180deg,#ffffff, #f8f9ff); overflow:hidden;">
      <div class="container">
        <div class="row align-items-center gy-4">
          <div class="col-lg-6" v-reveal>
            <span class="badge bg-primary-subtle text-primary mb-2 shadow-sm">AI Study Advisor • lumix</span>
            <h2 class="fw-bold mb-2">Not sure where to start?</h2>
            <p class="text-muted mb-2">Meet <strong>lumix</strong>, your AI study assistant. Answer a few quick questions and get a personalized study plan tailored to your grade and subjects in Egypt.</p>
            <ul class="text-muted small mb-4">
              <li>Weekly plan with realistic daily tasks</li>
              <li>Resource recommendations (videos, practice, notes)</li>
              <li>Actionable tips to stay consistent</li>
            </ul>
            <RouterLink :to="{ name: 'chat' }" class="btn btn-primary btn-lg px-4 btn-shine">
              <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Start with lumix
            </RouterLink>
          </div>
          <div class="col-lg-6" v-reveal>
            <div class="ratio ratio-4x3 rounded-4 shadow overflow-hidden bg-white position-relative ai-card" data-parallax data-speed="0.12">
              <img src="/images/حيران .jpg" alt="Confused student" class="w-100 h-100 animate-float" style="object-fit: cover;" />
              <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 80% 20%, rgba(13,110,253,.06), transparent 50%);"></div>
              <!-- floating chat chip from lumix -->
              <div class="position-absolute top-0 start-0 m-3">
                <div class="bg-white shadow-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 animate-float" style="animation-duration:6s;">
                  <span class="badge bg-primary">lumix</span>
                  <span class="small text-muted">Let’s craft your plan!</span>
                </div>
              </div>
              <!-- subtle glow ring -->
              <div class="position-absolute rounded-circle" style="right:-40px; bottom:-40px; width:160px; height:160px; background: radial-gradient(closest-side, rgba(13,110,253,.25), transparent 70%); filter: blur(2px);"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- decorative blobs -->
      <div class="blob blob-primary animate-float" style="width:160px;height:160px; left:-50px; bottom:20px; opacity:.7;" data-parallax data-speed="0.22"></div>
      <div class="blob blob-accent animate-wobble" style="width:120px;height:120px; right:-30px; top:10px; opacity:.8;" data-parallax data-speed="0.2"></div>
      <!-- sparkles -->
      <i class="fa-solid fa-star text-primary position-absolute animate-float" style="left:20%; top:10%; opacity:.5"></i>
      <i class="fa-solid fa-wand-magic-sparkles text-warning position-absolute animate-float" style="left:25%; top:18%; opacity:.4; animation-duration:7s;"></i>
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
import Footer from '../layouts/Footer.vue'
import auth from '../../store/auth'
export default {
  name: 'LandingPage',
  components: { Footer },
  data(){
    return {
      features: [
        { icon:'fa-solid fa-wand-magic-sparkles', title:'Personalized lessons', text:'Tailored to your pace.' },
        { icon:'fa-solid fa-robot', title:'AI tutor assistance', text:'Ask and learn instantly.' },
        { icon:'fa-solid fa-bell', title:'Smart reminders', text:'Stay on track.' },
        { icon:'fa-solid fa-circle-question', title:'Interactive Q&A', text:'Practice with feedback.' },
      ],
      faqs: [
        { q: 'How do I get started quickly?', a: 'Sign in and click "Browse Subjects" to pick a topic, or open the Chat to ask the AI tutor for a personalized plan.' },
        { q: 'How can I message the admin?', a: 'Open Messages and click "Message Admin" to start a conversation instantly.' },
        { q: 'Do you send study reminders?', a: 'Yes. Set a weekly goal and you will receive smart reminders based on your progress.' },
        { q: 'Is the AI tutor free?', a: 'Core features are free. Premium tiers may be added later for advanced capabilities.' },
        { q: 'Can I get a personalized study plan?', a: 'Yes. Ask lumix in the Chat and you will get a weekly plan tailored to your stage and subjects.' },
      ],
      steps: [
        { icon:'fa-solid fa-book-open', title:'Choose subject', text:'Pick your topic.' },
        { icon:'fa-solid fa-bolt', title:'Quick test', text:'Assess your level.' },
        { icon:'fa-solid fa-brain', title:'AI selects method', text:'Match your style.' },
        { icon:'fa-solid fa-calendar-check', title:'Reminders', text:'Plan to succeed.' },
        { icon:'fa-solid fa-graduation-cap', title:'Lessons', text:'Daily micro-learning.' },
      ],
      showVideo: false
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
    subjects(){
      const map = {
        Primary: [
          { name: 'Math', slug: 'math', icon: 'fa-solid fa-square-root-variable' },
          { name: 'Arabic', slug: 'arabic', icon: 'fa-solid fa-language' },
          { name: 'Science', slug: 'science', icon: 'fa-solid fa-flask' },
          { name: 'English', slug: 'english', icon: 'fa-solid fa-book' },
        ],
        Preparatory: [
          { name: 'Math', slug: 'math', icon: 'fa-solid fa-square-root-variable' },
          { name: 'Science', slug: 'science', icon: 'fa-solid fa-flask' },
          { name: 'History', slug: 'history', icon: 'fa-solid fa-landmark' },
          { name: 'English', slug: 'english', icon: 'fa-solid fa-book' },
          { name: 'Arabic', slug: 'arabic', icon: 'fa-solid fa-language' },
        ],
        Secondary: [
          { name: 'Math', slug: 'math', icon: 'fa-solid fa-square-root-variable' },
          { name: 'Physics', slug: 'physics', icon: 'fa-solid fa-atom' },
          { name: 'Chemistry', slug: 'chemistry', icon: 'fa-solid fa-vial' },
          { name: 'Biology', slug: 'biology', icon: 'fa-solid fa-dna' },
          { name: 'Arabic', slug: 'arabic', icon: 'fa-solid fa-language' },
          { name: 'English', slug: 'english', icon: 'fa-solid fa-book' },
        ],
      }
      return map[this.stage] || []
    }
  },
  methods: {
    openVideo(){ this.showVideo = true; document.body.classList.add('overflow-hidden') },
    closeVideo(){ this.showVideo = false; document.body.classList.remove('overflow-hidden') },
    onKey(e){ if (e.key === 'Escape') this.closeVideo() }
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
</style>
