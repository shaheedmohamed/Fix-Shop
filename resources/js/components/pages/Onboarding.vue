<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="p-4 p-lg-5 bg-white rounded-4 shadow-sm">
          <h2 class="fw-bold mb-3">Quick Level Check</h2>
          <p class="text-muted mb-4">Answer 5–7 short questions so we can tailor subjects and practice to your level.</p>

          <form @submit.prevent="submit">
            <div v-for="(q, i) in questions" :key="i" class="mb-4">
              <label class="fw-semibold mb-2 d-block">{{ i+1 }}. {{ q.text }}</label>

              <div v-if="q.type==='mcq'">
                <div class="form-check" v-for="(opt,j) in q.options" :key="j">
                  <input class="form-check-input" type="radio" :name="'q'+i" :id="'q'+i+'o'+j" :value="opt" v-model="answers[i]">
                  <label class="form-check-label" :for="'q'+i+'o'+j">{{ opt }}</label>
                </div>
              </div>

              <div v-else-if="q.type==='tf'" class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" :name="'q'+i" :id="'q'+i+'t'" value="True" v-model="answers[i]">
                  <label class="form-check-label" :for="'q'+i+'t'">True</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" :name="'q'+i" :id="'q'+i+'f'" value="False" v-model="answers[i]">
                  <label class="form-check-label" :for="'q'+i+'f'">False</label>
                </div>
              </div>

              <div v-else>
                <input class="form-control" type="text" v-model="answers[i]" placeholder="Your answer" />
              </div>
            </div>

            <div class="d-flex align-items-center gap-3">
              <button class="btn btn-primary px-4" type="submit">Finish</button>
              <small v-if="result" class="text-muted">Suggested stage: <strong>{{ result.stage }}</strong> (score {{ result.score }}/{{ questions.length }})</small>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import auth, { patchUser } from '../../store/auth'
export default {
  name: 'OnboardingPage',
  data(){
    return {
      questions: [
        { type:'mcq', text:'What is 12 × 8?', options:['80','88','96','108'], answer:'96' },
        { type:'tf', text:'Plants make food by photosynthesis.', answer:'True' },
        { type:'mcq', text:'Which is a noun?', options:['run','beauty','quickly','and'], answer:'beauty' },
        { type:'mcq', text:'H2O is the chemical formula for?', options:['Salt','Water','Oxygen','Carbon'], answer:'Water' },
        { type:'short', text:'In which direction does the sun rise?', answer:'east' },
        { type:'tf', text:'7 is a prime number.', answer:'True' },
      ],
      answers: {},
      result: null,
    }
  },
  methods: {
    score(){
      let s = 0
      this.questions.forEach((q, i) => {
        const a = (this.answers[i] || '').toString().trim().toLowerCase()
        const correct = (q.answer || '').toString().trim().toLowerCase()
        if (a === correct) s++
      })
      return s
    },
    mapToStage(score){
      const n = this.questions.length
      const pct = score / n
      if (pct < 0.4) return 'Primary'
      if (pct < 0.75) return 'Preparatory'
      return 'Secondary'
    },
    submit(){
      const s = this.score()
      const stage = this.mapToStage(s)
      this.result = { score: s, stage }
      // Persist to auth profile if logged in; otherwise keep in local storage stub
      if (auth.isAuthenticated()) {
        patchUser({ education_stage: stage })
      } else {
        const guest = JSON.parse(localStorage.getItem('sp_guest_profile')||'{}')
        guest.education_stage = stage
        localStorage.setItem('sp_guest_profile', JSON.stringify(guest))
      }
      this.$router.push({ name:'subjects' })
    }
  }
}
</script>

<style scoped>
</style>
