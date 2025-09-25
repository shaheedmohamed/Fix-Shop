<template>
  <div class="about-page">
    <!-- Premium Hero -->
    <section class="hero-section position-relative overflow-hidden py-5 py-lg-6">
      <div class="container position-relative">
        <div class="row align-items-center gy-4">
          <div class="col-lg-7" v-reveal>
            <span class="badge bg-gradient-primary text-white shadow-sm">Fix & Shop • About</span>
            <h1 class="display-5 fw-bold mt-3 text-gradient">Shaping the future of learning</h1>
            <p class="lead text-muted mt-2">We combine pedagogy, design, and AI to deliver learning that adapts to every student.</p>
            <div class="d-flex gap-3 mt-3">
              <RouterLink :to="{ name: 'chat' }" class="btn btn-primary btn-lg px-4 btn-shine">
                <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Try the AI Tutor
              </RouterLink>
              <RouterLink :to="{ name: 'features' }" class="btn btn-outline-primary btn-lg px-4">
                <i class="fa-solid fa-star me-2"></i>Our Features
              </RouterLink>
            </div>
          </div>
          <div class="col-lg-5" v-reveal>
            <div class="hero-card shadow-lg rounded-4 p-4 bg-white position-relative">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-xl text-primary"><i class="fa-solid fa-brain"></i></div>
                <div>
                  <h5 class="mb-1">Built with Care</h5>
                  <div class="text-muted small">Egypt-first. Student-focused. AI-powered.</div>
                </div>
              </div>
              <div class="glow-ring"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- decorative blobs -->
      <div class="blob blob-primary" style="--size: 220px; left:-60px; top:-40px;"></div>
      <div class="blob blob-accent" style="--size: 160px; right:-40px; bottom:-40px;"></div>
      <i class="fa-solid fa-star text-primary position-absolute" style="left:20%; top:10%; opacity:.4"></i>
      <i class="fa-solid fa-wand-magic-sparkles text-warning position-absolute" style="left:25%; top:18%; opacity:.35; animation: float 7s ease-in-out infinite"></i>
    </section>

    <!-- Mission & Values -->
    <section class="py-5">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-6" v-reveal>
            <div class="p-4 rounded-4 bg-white shadow-sm h-100 card-hover">
              <h5 class="fw-semibold mb-3"><i class="fa-solid fa-bullseye me-2"></i>Our Mission</h5>
              <p class="text-muted mb-0">Make high-quality learning personal, joyful, and effective for every student through adaptive AI and thoughtful design.</p>
            </div>
          </div>
          <div class="col-lg-6" v-reveal>
            <div class="p-4 rounded-4 bg-white shadow-sm h-100 card-hover">
              <h5 class="fw-semibold mb-3"><i class="fa-solid fa-gem me-2"></i>Our Values</h5>
              <ul class="text-muted mb-0 small">
                <li>Student-first decisions</li>
                <li>Evidence-based learning</li>
                <li>Simple, beautiful experiences</li>
                <li>Privacy and trust by default</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Student Journey -->
    <section class="py-5 bg-light border-top">
      <div class="container">
        <div class="text-center mb-4" v-reveal>
          <h2 class="fw-bold">Student Journey</h2>
          <p class="text-muted mb-0">From curiosity to confidence</p>
        </div>
        <div class="timeline premium">
          <div class="timeline-step" v-for="(t,k) in timeline" :key="k" v-reveal>
            <span class="dot"></span>
            <div class="box shadow-sm">
              <div class="fs-5 text-primary mb-1"><i :class="t.icon"></i></div>
              <strong>{{ t.title }}</strong>
              <div class="text-muted small">{{ t.text }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="text-center mb-5" v-reveal>
          <h2 class="fw-bold">Our Features</h2>
          <p class="text-muted">Explore the tools that make our platform amazing</p>
        </div>
        <div class="row g-4">
          <div class="col-sm-6 col-lg-4" v-for="(f,i) in features" :key="i" v-reveal>
            <div class="card border-0 shadow-sm h-100 card-hover">
              <div class="card-body p-4">
                <div class="fs-1 text-primary mb-3"><i :class="f.icon"></i></div>
                <h5 class="fw-semibold">{{ f.title }}</h5>
                <p class="text-muted mb-0">{{ f.text }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-5">
          <h3 class="fw-bold text-center mb-4" v-reveal>How it works</h3>
          <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-lg-4">
            <div class="col" v-for="(s,j) in steps" :key="j" v-reveal>
              <div class="h-100 text-center p-4 bg-white rounded-4 card-hover shadow-sm">
                <div class="fs-2 text-primary mb-2"><i :class="s.icon"></i></div>
                <div class="fw-semibold">{{ s.title }}</div>
                <div class="text-muted small">{{ s.text }}</div>
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
  name: 'AboutPage',
  components: { Footer },
  data(){
    return {
      timeline: [
        { icon:'fa-solid fa-book-open', title:'Choose subject', text:'Pick what to learn' },
        { icon:'fa-solid fa-bolt', title:'Quick test', text:'Gauge level' },
        { icon:'fa-solid fa-brain', title:'AI selects method', text:'Fit your style' },
        { icon:'fa-solid fa-graduation-cap', title:'Lessons', text:'Daily delivery' },
      ],
      features: [
        { icon: 'fa-solid fa-shopping-cart', title: 'Easy Shopping', text: 'Browse and purchase products with just a few clicks' },
        { icon: 'fa-solid fa-tools', title: 'Service Requests', text: 'Request any service and get offers from qualified providers' },
        { icon: 'fa-solid fa-comments', title: 'Direct Communication', text: 'Chat directly with sellers and service providers' },
        { icon: 'fa-solid fa-shield-alt', title: 'Secure Payments', text: 'Safe and secure payment processing for all transactions' },
        { icon: 'fa-solid fa-truck', title: 'Fast Delivery', text: 'Quick and reliable delivery to your doorstep' },
        { icon: 'fa-solid fa-star', title: 'Quality Assurance', text: 'All products and services are quality checked' }
      ],
      steps: [
        { icon: 'fa-solid fa-user-plus', title: 'Sign Up', text: 'Create your account' },
        { icon: 'fa-solid fa-search', title: 'Browse', text: 'Find what you need' },
        { icon: 'fa-solid fa-shopping-bag', title: 'Purchase', text: 'Buy or request service' },
        { icon: 'fa-solid fa-smile', title: 'Enjoy', text: 'Get your order' }
      ]
    }
  },
  methods: {
    // Methods for About page functionality
  }
}
</script>

<style scoped>
/* Hero */
.hero-section{ background: radial-gradient(1200px 600px at -10% -10%, rgba(13,110,253,.12), transparent 40%), radial-gradient(900px 500px at 110% 110%, rgba(111,66,193,.12), transparent 40%) }
.text-gradient{ background: linear-gradient(90deg,#0d6efd,#6f42c1); -webkit-background-clip: text; background-clip: text; color: transparent }
.hero-card{ overflow: hidden }
.hero-card .glow-ring{ position:absolute; right:-40px; bottom:-40px; width:180px; height:180px; border-radius:50%; background: radial-gradient(closest-side, rgba(13,110,253,.22), transparent 70%); filter: blur(2px) }
.blob{ position: absolute; width: var(--size); height: var(--size); border-radius: 50%; filter: blur(8px); opacity: .6 }
.blob-primary{ background: radial-gradient(circle at 30% 30%, rgba(13,110,253,.25), transparent 60%) }
.blob-accent{ background: radial-gradient(circle at 70% 70%, rgba(111,66,193,.25), transparent 60%) }
@keyframes float{ 0%,100%{ transform: translateY(0)} 50%{ transform: translateY(-6px)} }

/* Cards */
.card-hover{ transition: transform .2s ease, box-shadow .2s ease }
.card-hover:hover{ transform: translateY(-4px); box-shadow: 0 1rem 2rem rgba(0,0,0,.12) }

/* Timeline */
.timeline.premium{ position: relative; display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap: 1rem }
.timeline.premium:before{ content:''; position:absolute; left:0; right:0; top:18px; height:2px; background: linear-gradient(90deg, rgba(13,110,253,.25), rgba(111,66,193,.25)) }
.timeline.premium .timeline-step{ position: relative; display:flex; flex-direction: column; align-items:flex-start }
.timeline.premium .timeline-step .dot{ width:14px; height:14px; border-radius:50%; background:#0d6efd; border: 3px solid #fff; box-shadow: 0 0 0 4px rgba(13,110,253,.15); position: relative; z-index:2 }
.timeline.premium .timeline-step .box{ background:#fff; border-radius: .75rem; padding: .75rem .9rem; margin-top:.5rem }
@media (max-width: 992px){ .timeline.premium{ grid-template-columns: repeat(2, minmax(0,1fr)) } }

/* Center-focused carousel layout */
.center-focus .stage{ display:grid; grid-template-columns: 1fr minmax(320px,520px) 1fr; gap: 1.25rem; align-items: stretch; max-width: 1100px }
.center-focus .slide-item{ transition: transform .35s cubic-bezier(.22,.61,.36,1), opacity .35s ease, filter .35s ease }
.center-focus .slide-item .team-card{ height: 100%; position: relative; overflow: visible; padding-top: 52px }
.center-focus .slide-item .team-card.compact{ padding-top: 46px }
.center-focus .slide-item.left, .center-focus .slide-item.right{ opacity: .45; filter: blur(.2px); transform: scale(.94) translateY(6px) }
.center-focus .slide-item.center{ opacity: 1; filter: none; transform: scale(1) translateY(0) }

/* Avatar wrap: big circular avatar overlapping card */
.avatar-wrap{ position: absolute; left: 50%; transform: translateX(-50%); top: 0 }
.avatar{ display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; letter-spacing:.5px; border-radius:50%; position: relative; overflow:hidden; box-shadow: 0 10px 24px rgba(2,6,23,.18) }
.avatar-md{ width: 112px; height: 112px }
.avatar-xl{ width: 148px; height: 148px }
.avatar-img{ width:100%; height:100%; object-fit: cover }

/* Card visual styles */
.team-card{ background: #fff; border-radius: 1rem; padding: 1rem; box-shadow: 0 .5rem 1rem rgba(0,0,0,.06); transition: transform .2s ease, box-shadow .2s ease }
.team-card:hover{ transform: translateY(-6px); box-shadow: 0 1.25rem 2rem rgba(0,0,0,.12) }
.team-card.emphasized{ box-shadow: 0 1.25rem 2.25rem rgba(2,6,23,.16) }
.team-card.compact{ box-shadow: 0 .75rem 1.25rem rgba(2,6,23,.08) }
.team-card .avatar{ width:64px; height:64px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; letter-spacing:.5px }
.team-card .info{ margin-top:.75rem }
.team-card .links a{ color:#6c757d; margin-right:.5rem }
.team-card .links a:hover{ color:#0d6efd }

/* Member Modal */
.member-overlay{ position: fixed; inset: 0; background: rgba(2,6,23,.72); backdrop-filter: blur(4px); z-index: 1055; display:flex; align-items:center; justify-content:center; padding: 1rem }
.member-card{ width: min(840px, 96vw); background: #fff; border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,.3); overflow: hidden; position: relative }
.avatar-lg{ width:72px; height:72px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size: 1.1rem }
.avatar-xxl{ width: 180px; height: 180px; border-radius: 50%; box-shadow: 0 14px 28px rgba(2,6,23,.22) }
.modal-hero .social{ display:flex; gap:.6rem; justify-content:center }
.modal-hero .social a{ color:#fff; background: #0d6efd; width: 36px; height:36px; display:grid; place-items:center; border-radius:50%; box-shadow: 0 8px 18px rgba(13,110,253,.35) }
.modal-hero .social a:hover{ transform: translateY(-2px); background:#0b5ed7 }
</style>
