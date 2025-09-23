    <template>
  <div>
    <nav-bar />
    <!-- Hero -->
    <section id="top" class="hero-animated-bg py-5 py-lg-6 position-relative">
      <div class="container py-5">
        <div class="row align-items-center gy-4">
          <div class="col-lg-6" v-reveal>
            <span class="badge bg-gradient-primary text-white mb-3 shadow-sm">Smart Path · Digitopia 2025</span>
            <img src="/images/logo-smartpath.png" alt="Smart Path" class="hero-logo mb-2" />
            <h1 class="display-5 fw-bold text-gradient">AI that teaches you your way</h1>
            <p class="lead text-muted mt-3">
              Personalized lessons, an always-on AI tutor, and smart reminders – built for students, teachers, and parents.
            </p>
            <div class="d-flex gap-3 mt-4">
              <a href="#features" class="btn btn-primary btn-lg px-4 btn-shine">
                <i class="fa-solid fa-rocket me-2"></i>Get Started
              </a>
              <a href="#how" class="btn btn-outline-primary btn-lg px-4">
                <i class="fa-solid fa-circle-play me-2"></i>How it works
              </a>
            </div>
          </div>
          <div class="col-lg-6" v-reveal>
            <div class="position-relative">
              <div class="ratio ratio-16x9 rounded-4 shadow bg-white"></div>
              <div class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-light text-dark p-3 shadow">
                <i class="fa-solid fa-user-graduate text-primary me-2"></i> Personalized learning
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Feature Cards -->
    <section id="features" class="py-5">
      <div class="container">
        <div class="text-center mb-5" v-reveal>
          <h2 class="fw-bold">Everything you need to learn smarter</h2>
          <p class="text-muted">Built for growth with modern AI assistance</p>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3" v-reveal>
            <div class="card border-0 shadow-sm h-100 card-hover">
              <div class="card-body p-4">
                <div class="display-6 text-primary mb-3 icon-pulse"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                <h5 class="fw-semibold">Personalized lessons</h5>
                <p class="text-muted mb-0">Tailored to each student's level and pace.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3" v-reveal>
            <div class="card border-0 shadow-sm h-100 card-hover">
              <div class="card-body p-4">
                <div class="display-6 text-primary mb-3"><i class="fa-solid fa-robot"></i></div>
                <h5 class="fw-semibold">AI tutor assistance</h5>
                <p class="text-muted mb-0">Ask questions and get instant, friendly help.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3" v-reveal>
            <div class="card border-0 shadow-sm h-100 card-hover">
              <div class="card-body p-4">
                <div class="display-6 text-primary mb-3"><i class="fa-solid fa-bell"></i></div>
                <h5 class="fw-semibold">Smart reminders</h5>
                <p class="text-muted mb-0">Schedules and nudges that keep you on track.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3" v-reveal>
            <div class="card border-0 shadow-sm h-100 card-hover">
              <div class="card-body p-4">
                <div class="display-6 text-primary mb-3"><i class="fa-solid fa-circle-question"></i></div>
                <h5 class="fw-semibold">Interactive Q&A</h5>
                <p class="text-muted mb-0">Practice with quizzes and instant feedback.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section id="how" class="py-5 bg-light">
      <div class="container">
        <div class="text-center mb-4" v-reveal>
          <h2 class="fw-bold">How Smart Path works</h2>
        </div>
        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-4">
          <div class="col" v-reveal>
            <div class="h-100 text-center p-4 bg-white rounded-4 shadow-sm card-hover">
              <div class="fs-1 text-primary mb-2"><i class="fa-solid fa-book-open"></i></div>
              <h6 class="fw-semibold mb-1">Choose a subject</h6>
              <p class="text-muted small">Pick what you want to learn today.</p>
            </div>
          </div>
          <div class="col" v-reveal>
            <div class="h-100 text-center p-4 bg-white rounded-4 shadow-sm card-hover">
              <div class="fs-1 text-primary mb-2"><i class="fa-solid fa-bolt"></i></div>
              <h6 class="fw-semibold mb-1">Quick assessment</h6>
              <p class="text-muted small">We gauge your current level in minutes.</p>
            </div>
          </div>
          <div class="col" v-reveal>
            <div class="h-100 text-center p-4 bg-white rounded-4 shadow-sm card-hover">
              <div class="fs-1 text-primary mb-2"><i class="fa-solid fa-brain"></i></div>
              <h6 class="fw-semibold mb-1">AI picks your method</h6>
              <p class="text-muted small">Lessons align with your learning style.</p>
            </div>
          </div>
          <div class="col" v-reveal>
            <div class="h-100 text-center p-4 bg-white rounded-4 shadow-sm card-hover">
              <div class="fs-1 text-primary mb-2"><i class="fa-solid fa-calendar-check"></i></div>
              <h6 class="fw-semibold mb-1">Reminders + delivery</h6>
              <p class="text-muted small">We schedule and deliver bite-size lessons.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="py-4 border-top bg-white">
      <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
        <div class="text-muted">© {{ new Date().getFullYear() }} Smart Path</div>
        <div class="d-flex gap-3 fs-5">
          <a href="#" class="text-decoration-none"><i class="fa-brands fa-facebook"></i></a>
          <a href="#" class="text-decoration-none"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="#" class="text-decoration-none"><i class="fa-brands fa-instagram"></i></a>
        </div>
      </div>
    </footer>
    <bottom-nav />
  </div>
</template>

<script>
export default { name: 'Landing' }
</script>

<style scoped>
.py-lg-6{ padding-top:5rem; padding-bottom:5rem; }
</style>
