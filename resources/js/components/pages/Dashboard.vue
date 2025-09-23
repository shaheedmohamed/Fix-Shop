<template>
  <div class="py-5">
    <section class="container" v-reveal>
      <div class="text-center mb-4">
        <h1 class="fw-bold">Dashboard</h1>
        <p class="text-muted">Welcome back! Here's your learning snapshot.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm h-100 card-hover">
            <div class="card-body p-4 d-flex align-items-center">
              <div class="display-6 text-primary me-3 icon-pulse"><i class="fa-solid fa-book-open"></i></div>
              <div>
                <div class="fw-semibold">Lessons Today</div>
                <div class="text-muted">3 lessons assigned</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm h-100 card-hover">
            <div class="card-body p-4 d-flex align-items-center">
              <div class="display-6 text-primary me-3"><i class="fa-solid fa-chart-line"></i></div>
              <div>
                <div class="fw-semibold">Progress</div>
                <div class="text-muted">76% this week</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm h-100 card-hover">
            <div class="card-body p-4 d-flex align-items-center">
              <div class="display-6 text-primary me-3"><i class="fa-solid fa-bell"></i></div>
              <div>
                <div class="fw-semibold">Reminders</div>
                <div class="text-muted">Next at 6:00 PM</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mt-1">
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <h5 class="fw-semibold mb-3"><i class="fa-solid fa-list-check me-2"></i>Upcoming Lessons</h5>
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Algebra: Linear Equations <span class="badge bg-primary rounded-pill">Today</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Science: Photosynthesis <span class="badge bg-secondary rounded-pill">Tomorrow</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  English: Reading Comprehension <span class="badge bg-success rounded-pill">Wed</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <h5 class="fw-semibold mb-3"><i class="fa-solid fa-award me-2"></i>Badges</h5>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-gradient-primary text-white">Streak x7</span>
                <span class="badge bg-gradient-primary text-white">Algebra Rookie</span>
                <span class="badge bg-gradient-primary text-white">Reader</span>
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
export default { name:'DashboardPage', components:{ Footer } }
</script>
