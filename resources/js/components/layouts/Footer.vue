<template>
  <footer class="footer-gradient text-white pt-5 pt-lg-6 mt-5 position-relative overflow-hidden">
    <!-- Glow shapes -->
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>

    <div class="container position-relative">
      <div class="row g-5">
        <!-- Brand -->
        <div class="col-12 col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-graduation-cap fs-3"></i>
            <h5 class="mb-0 fw-bold">Smart Path</h5>
          </div>
          <p class="text-white-50 mb-4">Personalized learning, an always-on AI tutor, and smart reminders to keep you on track.</p>

          <div class="d-flex flex-wrap gap-3 fs-5">
            <a href="#" class="icon-link"><i class="fa-brands fa-facebook"></i></a>
            <a href="#" class="icon-link"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" class="icon-link"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="icon-link"><i class="fa-brands fa-linkedin"></i></a>
            <a href="#" class="icon-link"><i class="fa-brands fa-youtube"></i></a>
            <a href="#" class="icon-link"><i class="fa-brands fa-tiktok"></i></a>
            <a href="#" class="icon-link"><i class="fa-brands fa-github"></i></a>
            <a href="#" class="icon-link"><i class="fa-brands fa-discord"></i></a>
            <a href="#" class="icon-link"><i class="fa-brands fa-telegram"></i></a>
          </div>
        </div>

        <!-- Links -->
        <div class="col-6 col-lg-2">
          <h6 class="text-uppercase small fw-bold mb-3">Product</h6>
          <ul class="list-unstyled m-0">
            <li><RouterLink to="/features" class="link">Features</RouterLink></li>
            <li><RouterLink :to="{ name:'subjects' }" class="link">Subjects</RouterLink></li>
            <li><RouterLink :to="{ name:'chat' }" class="link">AI Tutor</RouterLink></li>
            <li><RouterLink to="/pricing" class="link">Pricing</RouterLink></li>
          </ul>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="text-uppercase small fw-bold mb-3">Learn</h6>
          <ul class="list-unstyled m-0">
            <li><RouterLink to="/blog" class="link">Blog</RouterLink></li>
            <li><RouterLink to="/guides" class="link">Guides</RouterLink></li>
            <li><RouterLink to="/roadmap" class="link">Roadmap</RouterLink></li>
            <li><RouterLink to="/changelog" class="link">Changelog</RouterLink></li>
          </ul>
        </div>
        <div class="col-12 col-lg-4">
          <!-- Right column switches based on auth state -->
          <div v-if="!isLoggedIn">
            <h6 class="text-uppercase small fw-bold mb-3">Get Started</h6>
            <p class="text-white-50 small">Create your plan in minutes. Sign in to unlock your AI tutor and subjects.</p>
            <div class="d-flex gap-2">
              <RouterLink :to="{ name: 'login' }" class="btn btn-primary btn-lg px-4">Login</RouterLink>
              <RouterLink :to="{ name: 'register' }" class="btn btn-outline-light btn-lg px-4">Sign Up</RouterLink>
            </div>
            <small class="d-block mt-2 text-white-50">No account yet? Signing up is free.</small>
            <div class="mt-3 small text-white-50">
              <i class="fa-solid fa-sparkles me-1"></i>{{ guestNudge }}
            </div>
          </div>
          <div v-else>
            <h6 class="text-uppercase small fw-bold mb-3">Quick links</h6>
            <ul class="list-unstyled m-0">
              <li><RouterLink :to="{ name:'subjects' }" class="link">Your Subjects</RouterLink></li>
              <li><RouterLink :to="{ name:'chat' }" class="link">Ask AI Tutor</RouterLink></li>
              <li><RouterLink :to="{ name:'messages' }" class="link">Messages</RouterLink></li>
            </ul>
          </div>
        </div>
      </div>


      <!-- Bottom bar -->
      <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3 py-3">
        <div class="text-white-50">© {{ new Date().getFullYear() }} Smart Path. All rights reserved.</div>
        <div class="d-flex flex-wrap gap-3">
          <RouterLink to="/terms" class="link">Terms</RouterLink>
          <RouterLink to="/privacy" class="link">Privacy</RouterLink>
          <RouterLink to="/contact" class="link">Contact</RouterLink>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import auth from '../../store/auth'
export default {
  name: 'FooterLayout',
  computed: {
    isLoggedIn(){ return auth.isAuthenticated() },
    guestNudge(){
      const tips = [
        'ابدأ اليوم بسؤال واحد للـAI… خطوة صغيرة تصنع فرق كبير.',
        'جرّب بناء خطة أسبوعية الآن وخلّي الذكاء الاصطناعي يساعدك تمشي علىها.',
        'اسأل عن درسك المفضل وخد شرح بسيط مع مثال عملي.',
        'مفيش وقت؟ اسأل الـAI عن 15 دقيقة مفيدة النهارده.',
      ]
      const d = new Date(); const idx = (d.getFullYear()*100 + (d.getMonth()+1)*3 + d.getDate()) % tips.length
      return tips[idx]
    }
  }
}
</script>

<style scoped>
.footer-gradient{
  background: radial-gradient(1200px 600px at -10% 0%, #0b1220 0, rgba(11,18,32,.8) 35%, rgba(11,18,32,1) 60%),
              linear-gradient(180deg, #0b1220 0%, #0e1630 100%);
}
.glow{ position:absolute; filter:blur(48px); opacity:.35; pointer-events:none; }
.glow-1{ width:380px; height:380px; left:-120px; bottom:-80px; background: radial-gradient(closest-side, #0d6efd, transparent 70%); }
.glow-2{ width:280px; height:280px; right:-80px; top:-60px; background: radial-gradient(closest-side, #6f42c1, transparent 70%); }
.icon-link{ color: rgba(255,255,255,.75); transition: .2s ease; }
.icon-link:hover{ color:#fff; transform: translateY(-2px); }
.link{ color: rgba(255,255,255,.75); text-decoration: none; display:inline-block; padding:.25rem 0; }
.link:hover{ color: #fff; text-decoration: underline; }
.border-white-10{ border-color: rgba(255,255,255,.1)!important; }
.placeholder-white::placeholder{ color: rgba(255,255,255,.6); }
</style>
