<template>
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
          <div class="text-center" v-reveal>
            <span class="badge bg-gradient-primary text-white shadow-sm">Digitopia 2025</span>
            <h1 class="fw-bold mt-3">About Smart Path</h1>
            <p class="text-muted">Our vision is to empower every learner with an AI that teaches their way.</p>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-6" v-reveal>
          <div class="p-4 rounded-4 bg-white shadow-sm h-100">
            <h5 class="fw-semibold mb-3"><i class="fa-solid fa-people-group me-2"></i>Team & Competition</h5>
            <p class="text-muted">We are a passionate team competing in Digitopia 2025, building tools for students, teachers, and parents.</p>
            <p class="text-muted mb-0">Smart Path blends pedagogy with AI to personalize learning journeys at scale.</p>
          </div>
        </div>
        <div class="col-lg-6" v-reveal>
          <div class="p-4 rounded-4 bg-white shadow-sm h-100">
            <h5 class="fw-semibold mb-3"><i class="fa-solid fa-bullseye me-2"></i>Vision</h5>
            <p class="text-muted">Learning should be personal, adaptive, and supportive. Our AI respects different learning styles and provides actionable insights.</p>
          </div>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-12" v-reveal>
          <h4 class="fw-bold mb-3">Student Journey Timeline</h4>
          <div class="timeline">
            <div class="timeline-step">
              <span class="dot"></span>
              <div class="ms-4">
                <strong><i class="fa-solid fa-book-open me-2"></i>Choose subject</strong>
                <div class="text-muted small">Student selects a topic to learn.</div>
              </div>
            </div>
            <div class="timeline-step">
              <span class="dot"></span>
              <div class="ms-4">
                <strong><i class="fa-solid fa-bolt me-2"></i>Quick assessment</strong>
                <div class="text-muted small">Short quiz determines level.</div>
              </div>
            </div>
            <div class="timeline-step">
              <span class="dot"></span>
              <div class="ms-4">
                <strong><i class="fa-solid fa-brain me-2"></i>AI chooses method</strong>
                <div class="text-muted small">Content styled to learning preference.</div>
              </div>
            </div>
            <div class="timeline-step">
              <span class="dot"></span>
              <div class="ms-4">
                <strong><i class="fa-solid fa-calendar-check me-2"></i>Reminders sent</strong>
                <div class="text-muted small">Smart nudges keep momentum.</div>
              </div>
            </div>
            <div class="timeline-step">
              <span class="dot"></span>
              <div class="ms-4">
                <strong><i class="fa-solid fa-graduation-cap me-2"></i>Lessons delivered</strong>
                <div class="text-muted small">Bite-size learning each day.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script>
export default { name: 'About' }
</script>
