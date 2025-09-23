<template>
  <div class="py-5">
    <section class="container">
      <div class="text-center mb-5" v-reveal>
        <h1 class="fw-bold">Features</h1>
        <p class="text-muted">Explore the tools that make learning smarter</p>
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

      <div id="how" class="mt-5">
        <h2 class="fw-bold text-center mb-4" v-reveal>How it works in 4 steps</h2>
        <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-lg-4">
          <div class="col" v-for="(s,j) in steps" :key="j" v-reveal>
            <div class="h-100 text-center p-4 bg-light rounded-4 card-hover">
              <div class="fs-2 text-primary mb-2"><i :class="s.icon"></i></div>
              <div class="fw-semibold">{{ s.title }}</div>
              <div class="text-muted small">{{ s.text }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="timeline mt-5" v-reveal>
        <div class="timeline-step" v-for="(t,k) in timeline" :key="k">
          <span class="dot"></span>
          <div class="ms-4">
            <strong><i :class="t.icon" class="me-2"></i>{{ t.title }}</strong>
            <div class="text-muted small">{{ t.text }}</div>
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
  name: 'FeaturesPage',
  components: { Footer },
  data(){
    return {
      features: [
        { icon:'fa-solid fa-user-gear', title:'Adaptive', text:'Level adjusts to your mastery.' },
        { icon:'fa-solid fa-chart-line', title:'Progress', text:'Visualize growth.' },
        { icon:'fa-solid fa-language', title:'Multilingual', text:'Learn in your language.' },
        { icon:'fa-solid fa-shield-halved', title:'Privacy', text:'Safe for students.' },
        { icon:'fa-solid fa-cloud-arrow-down', title:'Offline', text:'Study anywhere.' },
        { icon:'fa-solid fa-gamepad', title:'Gamified', text:'Earn badges.' },
      ],
      steps: [
        { icon:'fa-solid fa-book-open', title:'Choose subject', text:'Pick topic' },
        { icon:'fa-solid fa-bolt', title:'Quick test', text:'Assess level' },
        { icon:'fa-solid fa-brain', title:'AI method', text:'Match style' },
        { icon:'fa-solid fa-calendar-check', title:'Reminders', text:'Stay on track' },
      ],
      timeline: [
        { icon:'fa-solid fa-book-open', title:'Start', text:'Choose a subject' },
        { icon:'fa-solid fa-clipboard-check', title:'Assess', text:'Quick placement' },
        { icon:'fa-solid fa-brain', title:'Personalize', text:'AI selects strategy' },
        { icon:'fa-solid fa-bell', title:'Remind', text:'Smart schedule' },
        { icon:'fa-solid fa-graduation-cap', title:'Deliver', text:'Lessons daily' },
      ]
    }
  }
}
</script>
