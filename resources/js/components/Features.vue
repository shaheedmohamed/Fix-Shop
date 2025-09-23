<template>
  <section class="py-5">
    <div class="container">
      <div class="text-center mb-5" v-reveal>
        <h1 class="fw-bold">Features</h1>
        <p class="text-muted">Designed to keep learners engaged and progressing</p>
      </div>

      <!-- Feature Grid -->
      <div class="row g-4">
        <div class="col-sm-6 col-lg-4" v-for="(f, i) in features" :key="i" v-reveal>
          <div class="card border-0 shadow-sm h-100 card-hover">
            <div class="card-body p-4">
              <div class="fs-1 text-primary mb-3"><i :class="f.icon"></i></div>
              <h5 class="fw-semibold">{{ f.title }}</h5>
              <p class="text-muted mb-0">{{ f.text }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- How it works mini -->
      <div id="how-mini" class="mt-5">
        <h2 class="fw-bold text-center mb-4" v-reveal>How it works</h2>
        <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-lg-4">
          <div class="col" v-for="(s, j) in steps" :key="j" v-reveal>
            <div class="h-100 text-center p-4 bg-light rounded-4 card-hover">
              <div class="fs-2 text-primary mb-2"><i :class="s.icon"></i></div>
              <div class="fw-semibold">{{ s.title }}</div>
              <div class="text-muted small">{{ s.text }}</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script>
export default {
  name: 'Features',
  data(){
    return {
      features: [
        { icon:'fa-solid fa-user-gear', title:'Adaptive Learning', text:'Dynamic difficulty and pacing.' },
        { icon:'fa-solid fa-chart-line', title:'Progress Tracking', text:'Visualize achievements and gaps.' },
        { icon:'fa-solid fa-language', title:'Multilingual', text:'Content available in multiple languages.' },
        { icon:'fa-solid fa-shield-halved', title:'Safe & Secure', text:'Privacy-first for students and families.' },
        { icon:'fa-solid fa-cloud-arrow-down', title:'Offline Packs', text:'Download lessons for later.' },
        { icon:'fa-solid fa-gamepad', title:'Gamified Quests', text:'Earn badges and level up.' },
      ],
      steps: [
        { icon:'fa-solid fa-book-open', title:'Choose subject', text:'Select topic to start.' },
        { icon:'fa-solid fa-bolt', title:'Quick assess', text:'Short skill check.' },
        { icon:'fa-solid fa-brain', title:'AI method', text:'Best learning path.' },
        { icon:'fa-solid fa-calendar-check', title:'Reminders', text:'Stay on schedule.' },
      ]
    }
  }
}
</script>
