<template>
  <div class="min-vh-100 bg-light">
    <section class="container py-5">
      <div class="bg-white rounded-3 shadow-sm p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center gap-2">
            <h1 class="h4 fw-bold mb-0">Not sure where to start? — AI Study Advisor</h1>
            <span class="badge bg-primary-subtle text-primary">lumix</span>
          </div>
          <RouterLink :to="{ name: 'home' }" class="btn btn-outline-secondary">Back to Home</RouterLink>
        </div>

        <p class="text-muted">Answer a few short questions. lumix will build a personalized plan. Use Next/Previous to navigate.</p>

        <!-- Wizard Steps -->
        <div class="mb-3">
          <div class="d-flex align-items-center gap-2 small">
            <span v-for="(s, i) in steps" :key="i" class="badge" :class="i === step ? 'bg-primary' : 'bg-secondary-subtle text-secondary'">Step {{ i+1 }}</span>
          </div>
        </div>

        <form @submit.prevent="onSubmitClick" class="row g-3">
          <!-- Step 1: Basics -->
          <template v-if="step === 0">
            <div class="col-md-4">
              <label class="form-label">Education stage</label>
              <select v-model="form.stage" class="form-select" required>
                <option value="">Select</option>
                <option>Primary</option>
                <option>Preparatory</option>
                <option>Secondary</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Grade</label>
              <input v-model="form.grade" type="text" class="form-control" placeholder="e.g., Grade 5 / Prep 2 / Sec 3" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Plan language</label>
              <select v-model="form.language" class="form-select">
                <option value="en">English</option>
                <option value="ar">Arabic</option>
              </select>
            </div>
          </template>

          <!-- Step 2: Subjects & Time -->
          <template v-else-if="step === 1">
            <div class="col-12">
              <label class="form-label">Subjects</label>
              <div class="d-flex flex-wrap gap-2">
                <label v-for="s in allSubjects" :key="s" class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" :value="s" v-model="form.subjects" />
                  <span class="form-check-label">{{ s }}</span>
                </label>
              </div>
              <small class="text-muted d-block">Select one or more subjects.</small>
            </div>
            <div class="col-md-6">
              <label class="form-label">Available weekly hours</label>
              <input v-model="form.weekly_hours" type="number" min="1" class="form-control" placeholder="e.g., 10" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Days available</label>
              <input v-model="form.days_available" type="text" class="form-control" placeholder="e.g., Sat to Thu" />
            </div>
          </template>

          <!-- Step 3: Preferences -->
          <template v-else-if="step === 2">
            <div class="col-md-6">
              <label class="form-label">Preferred study style</label>
              <select v-model="form.style" class="form-select" required>
                <option value="">Select</option>
                <option>Videos</option>
                <option>Practice exercises</option>
                <option>Written summaries</option>
                <option>Mix</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Preferred study time</label>
              <select v-model="form.daytime" class="form-select">
                <option value="">No preference</option>
                <option>Morning</option>
                <option>Afternoon</option>
                <option>Evening</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Strong topics (optional)</label>
              <input v-model="form.strengths" type="text" class="form-control" placeholder="e.g., Algebra, Grammar" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Weak topics (optional)</label>
              <input v-model="form.weaknesses" type="text" class="form-control" placeholder="e.g., Geometry, Reading" />
            </div>
          </template>

          <!-- Step 4: Goals & Constraints -->
          <template v-else-if="step === 3">
            <div class="col-md-6">
              <label class="form-label">Goal</label>
              <input v-model="form.goal" type="text" class="form-control" placeholder="e.g., Midterms prep / +15% this term" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Upcoming exam date (optional)</label>
              <input v-model="form.exam_date" type="date" class="form-control" />
            </div>
            <div class="col-12">
              <label class="form-label">Constraints or notes (optional)</label>
              <textarea v-model="form.notes" class="form-control" rows="2" placeholder="e.g., weekends only, sports training Tue/Thu"></textarea>
            </div>
          </template>

          <!-- Step 5: Review -->
          <template v-else-if="step === 4">
            <div class="col-12">
              <div class="alert alert-secondary">
                <div class="fw-semibold mb-1">Review your inputs</div>
                <div class="small text-muted">Stage: {{ form.stage }} | Grade: {{ form.grade }} | Subjects: {{ form.subjects.join(', ') || '-' }}</div>
                <div class="small text-muted">Style: {{ form.style || '-' }} | Goal: {{ form.goal || '-' }}</div>
                <div class="small text-muted">Weekly hours: {{ form.weekly_hours || '-' }} | Days: {{ form.days_available || '-' }} | Pref time: {{ form.daytime || '-' }}</div>
                <div class="small text-muted">Strengths: {{ form.strengths || '-' }} | Weaknesses: {{ form.weaknesses || '-' }}</div>
                <div class="small text-muted">Notes: {{ form.notes || '-' }} | Language: {{ form.language }}</div>
              </div>
            </div>
          </template>

          <!-- Wizard controls -->
          <div class="col-12 d-flex align-items-center justify-content-between mt-2">
            <button type="button" class="btn btn-outline-secondary" :disabled="step===0 || loading" @click="prev">Previous</button>
            <div class="d-flex align-items-center gap-2">
              <span v-if="error" class="text-danger small me-2">{{ error }}</span>
              <button v-if="step<steps.length-1" type="button" class="btn btn-primary" :disabled="loading" @click="next">Next</button>
              <button v-else class="btn btn-primary" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Generate AI Plan
              </button>
            </div>
          </div>
        </form>

        <!-- Result -->
        <div v-if="plan" class="mt-4">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-semibold mb-2">Suggested Plan</h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary" @click="copyPlan">Copy</button>
              <button class="btn btn-sm btn-outline-secondary" @click="downloadPlan">Download .md</button>
              <button class="btn btn-sm btn-outline-secondary" @click="printPlan">Print / Save as PDF</button>
              <button class="btn btn-sm btn-primary" @click="openFeedback">Request changes</button>
            </div>
          </div>
          <div class="card border-0 shadow-sm" id="planCard">
            <div class="card-body">
              <template v-if="planJson && planJson.weeks && planJson.weeks.length">
                <div class="mb-3">
                  <h6 class="fw-semibold mb-0">{{ planJson.title || 'Study Plan' }}</h6>
                  <small class="text-muted">Generated by lumix</small>
                </div>
                <div v-for="(w, wi) in planJson.weeks" :key="wi" class="mb-4">
                  <div class="fw-semibold mb-2">Week {{ w.week }}</div>
                  <div class="table-responsive">
                    <table class="table table-striped align-middle">
                      <thead>
                        <tr>
                          <th style="width: 120px;">Day</th>
                          <th>Tasks</th>
                          <th style="width: 140px;">Duration</th>
                          <th style="width: 220px;">Resource</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(d, di) in (w.days || [])" :key="di">
                          <td class="fw-semibold">{{ d.day }}</td>
                          <td>
                            <ul class="mb-0 ps-3">
                              <li v-for="(it, ii) in (d.items || [])" :key="ii">
                                <span class="fw-semibold" v-if="it.subject">{{ it.subject }}: </span>{{ it.task }}
                              </li>
                            </ul>
                          </td>
                          <td>
                            <div v-for="(it, ii) in (d.items || [])" :key="'dur'+ii">{{ it.duration_minutes ? it.duration_minutes + ' min' : '-' }}</div>
                          </td>
                          <td>
                            <div v-for="(it, ii) in (d.items || [])" :key="'res'+ii">{{ it.resource || '-' }}</div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-if="w.tips?.length" class="small text-muted">Tips: {{ w.tips.join(' • ') }}</div>
                </div>
              </template>
              <template v-else>
                <pre class="mb-0" style="white-space: pre-wrap">{{ plan }}</pre>
              </template>
            </div>
          </div>
        </div>

        <!-- Feedback Modal -->
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Request plan changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <textarea v-model="feedbackText" class="form-control" rows="4" placeholder="Describe what you want to change... e.g., more practice for Math, shorter sessions on weekdays"></textarea>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" :disabled="sendingFeedback" @click="sendFeedback">
                  <span v-if="sendingFeedback" class="spinner-border spinner-border-sm me-2"></span>
                  Send request
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Dev notes -->
        <!--
          Setup:
            - Backend route: POST /api/ai/study-plan
            - Requires .env: OPENROUTER_API_KEY=sk-or-... (optional: OPENROUTER_BASE_URL, OPENROUTER_MODEL)
            - See controller: app/Http/Controllers/AIController.php
        -->
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios'
import messages from '../../store/messages'
// Bootstrap modal API (assumes bootstrap bundle present on page)
let BSModal
export default {
  name: 'AdvisorPage',
  data(){
    return {
      step: 0,
      steps: [ 'Basics', 'Subjects & Time', 'Preferences', 'Goals', 'Review' ],
      form: {
        stage: '',
        grade: '',
        subjects: [],
        // legacy field kept for API compatibility
        time: '',
        // new fields to enrich the prompt (optional in backend)
        weekly_hours: '',
        days_available: '',
        daytime: '',
        strengths: '',
        weaknesses: '',
        exam_date: '',
        notes: '',
        style: '',
        goal: '',
        language: 'en'
      },
      allSubjects: ['Arabic','English','Math','Science','Social Studies','Physics','Chemistry','Biology','Geography','History'],
      loading: false,
      error: '',
      errorDetails: null,
      plan: '',
      planJson: null,
      feedbackText: '',
      sendingFeedback: false,
    }
  },
  methods: {
    next(){
      if (this.step === 0) {
        if (!this.form.stage || !this.form.grade) { this.error = 'Please choose stage and grade.'; return }
      }
      if (this.step === 1) {
        if (!this.form.subjects.length) { this.error = 'Please select at least one subject.'; return }
      }
      if (this.step === 2) {
        if (!this.form.style) { this.error = 'Please choose a preferred study style.'; return }
      }
      if (this.step === 3) {
        if (!this.form.goal) { this.error = 'Please enter your main goal.'; return }
      }
      this.error = ''
      if (this.step < this.steps.length - 1) this.step++
    },
    prev(){ if (this.step>0 && !this.loading) { this.error = ''; this.step-- } },
    onSubmitClick(){
      if (this.step < this.steps.length - 1) { this.next(); return }
      this.submit()
    },
    async submit(){
      this.error = ''
      this.plan = ''
      this.planJson = null
      this.errorDetails = null
      // derive legacy time string from weekly_hours if empty, to satisfy backend
      if (!this.form.time && this.form.weekly_hours) {
        this.form.time = `${this.form.weekly_hours} hours weekly`
      }
      // final validation
      if (!this.form.stage || !this.form.grade || !this.form.style || !this.form.goal || this.form.subjects.length===0) {
        this.error = 'Please complete required fields.'
        return
      }
      this.loading = true
      try{
        const { data } = await axios.post('/api/ai/study-plan', this.form)
        this.plan = data.plan || ''
        // Try to extract a JSON schedule from a fenced code block
        const m = this.plan.match(/```json\s*([\s\S]*?)\s*```/i)
        if (m && m[1]) {
          try { this.planJson = JSON.parse(m[1]) } catch (_) { this.planJson = null }
        }
      }catch(e){
        const d = e?.response?.data
        this.error = d?.message || 'Failed to generate the plan. Please check the server API key settings.'
        this.errorDetails = d ? { status: d.status, details: d.details, hint: d.hint } : null
      }finally{
        this.loading = false
      }
    },
    copyPlan(){
      if (!this.plan) return
      navigator.clipboard?.writeText(this.plan)
    },
    downloadPlan(){
      const blob = new Blob([this.plan], { type: 'text/markdown;charset=utf-8' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = 'study-plan.md'
      a.click()
      URL.revokeObjectURL(url)
    },
    printPlan(){
      // Simple print – user can save as PDF
      window.print()
    },
    openFeedback(){
      this.feedbackText = ''
      try {
        if (!BSModal) BSModal = window.bootstrap?.Modal
        const el = document.getElementById('feedbackModal')
        if (BSModal && el) new BSModal(el).show()
      } catch (_) {}
    },
    async sendFeedback(){
      if (!this.feedbackText?.trim()) return
      this.sendingFeedback = true
      try {
        const title = this.planJson?.title || 'Study Plan'
        const msg = `Request to adjust plan: ${title}\n\nDetails: ${this.feedbackText.trim()}`
        await messages.startConversationWithAdmin(msg)
        // close modal
        const el = document.getElementById('feedbackModal')
        const inst = window.bootstrap?.Modal?.getInstance?.(el)
        inst?.hide?.()
      } finally {
        this.sendingFeedback = false
      }
    }
  }
}
</script>
