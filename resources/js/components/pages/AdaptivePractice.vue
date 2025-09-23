<template>
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="p-4 bg-white rounded-4 shadow-sm">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Adaptive Practice</h4>
            <select v-model="difficulty" class="form-select w-auto">
              <option value="easy">Easy</option>
              <option value="medium">Medium</option>
              <option value="hard">Hard</option>
            </select>
          </div>
          <div v-for="(q,i) in currentSet" :key="i" class="mb-4">
            <div class="fw-semibold mb-2">{{ i+1 }}. {{ q.text }}</div>
            <div v-if="q.type==='mcq'">
              <div class="form-check" v-for="(opt,j) in q.options" :key="j">
                <input class="form-check-input" type="radio" :name="'p'+i" :id="'p'+i+'o'+j" :value="opt" v-model="responses[i]">
                <label class="form-check-label" :for="'p'+i+'o'+j">{{ opt }}</label>
              </div>
            </div>
            <div v-else-if="q.type==='tf'" class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" :name="'p'+i" :id="'p'+i+'t'" value="True" v-model="responses[i]">
                <label class="form-check-label" :for="'p'+i+'t'">True</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" :name="'p'+i" :id="'p'+i+'f'" value="False" v-model="responses[i]">
                <label class="form-check-label" :for="'p'+i+'f'">False</label>
              </div>
            </div>
            <div v-else>
              <input class="form-control" type="text" v-model="responses[i]" placeholder="Your answer" />
            </div>
            <div v-if="checked" class="mt-2 small">
              <span :class="isCorrect(q, responses[i]) ? 'text-success' : 'text-danger'">
                {{ isCorrect(q, responses[i]) ? 'Correct' : 'Incorrect' }}
              </span>
              <span class="text-muted ms-2">{{ q.explain }}</span>
            </div>
            <div v-if="checked" class="mt-2 d-flex flex-wrap gap-2">
              <button class="btn btn-sm btn-outline-danger" @click="schedule(i,'again')">Again</button>
              <button class="btn btn-sm btn-outline-warning" @click="schedule(i,'hard')">Hard</button>
              <button class="btn btn-sm btn-outline-success" @click="schedule(i,'good')">Good</button>
              <button class="btn btn-sm btn-outline-primary" @click="schedule(i,'easy')">Easy</button>
            </div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" @click="check">Check</button>
            <button class="btn btn-primary" @click="nextSet">Next Set</button>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="p-4 bg-white rounded-4 shadow-sm">
          <h6 class="fw-bold mb-2">Explain styles</h6>
          <textarea v-model="paragraph" rows="5" class="form-control mb-2" placeholder="Paste a paragraph to explain..."></textarea>
          <div class="d-flex flex-wrap gap-2 mb-2">
            <button class="btn btn-sm btn-outline-secondary" @click="explain('simple')">اشرح ببساطة</button>
            <button class="btn btn-sm btn-outline-secondary" @click="explain('analogy')">تشبيه</button>
            <button class="btn btn-sm btn-outline-secondary" @click="explain('steps')">خطوات</button>
          </div>
          <div v-if="explanation" class="alert alert-secondary small mb-0">{{ explanation }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import auth from '../../store/auth'
import axios from 'axios'
export default {
  name: 'AdaptivePracticePage',
  data(){
    const stage = auth.state.user?.education_stage || 'Primary'
    return {
      difficulty: stage === 'Secondary' ? 'medium' : 'easy',
      bank: {
        easy: [
          { type:'mcq', text:'5 + 7 = ?', options:['10','11','12','13'], answer:'12', explain:'5 + 7 = 12.' },
          { type:'tf', text:'The sun rises in the east.', answer:'True', explain:'It rises in the east and sets in the west.' },
        ],
        medium: [
          { type:'mcq', text:'Which gas do plants absorb?', options:['Oxygen','Nitrogen','Carbon dioxide','Helium'], answer:'Carbon dioxide', explain:'Plants absorb CO₂ and release O₂.' },
          { type:'short', text:'What is the square root of 81?', answer:'9', explain:'9 × 9 = 81.' },
        ],
        hard: [
          { type:'mcq', text:'Derivative of x^2 is?', options:['x','2x','x^3','2'], answer:'2x', explain:'d/dx x^2 = 2x.' },
          { type:'tf', text:'Photosynthesis occurs in mitochondria.', answer:'False', explain:'It occurs in chloroplasts.' },
        ]
      },
      idx: 0,
      responses: {},
      checked: false,
      paragraph: '',
      explanation: '',
      due: [],
      loadingDue: false,
    }
  },
  computed: {
    currentSet(){
      const set = this.bank[this.difficulty]
      return set
    }
  },
  methods: {
    isCorrect(q, a){
      return (a||'').toString().trim().toLowerCase() === (q.answer||'').toString().trim().toLowerCase()
    },
    check(){ this.checked = true },
    nextSet(){ this.responses = {}; this.checked = false; this.idx++ },
    itemId(i){
      // stable id for review scheduling for current bank item
      return `practice:${this.difficulty}:${i}`
    },
    async schedule(i, quality){
      try{
        await axios.post('/api/reviews/schedule', {
          item_id: this.itemId(i),
          item_type: 'practice',
          subject: this.difficulty,
          quality
        })
      }catch(e){
        // no-op for guests or on error
      }
    },
    async loadDue(){
      this.loadingDue = true
      try{
        const { data } = await axios.get('/api/reviews/next')
        this.due = Array.isArray(data) ? data : []
        // If any due item matches our naming, pick its difficulty to start
        const first = this.due.find(d => (d.item_id||'').startsWith('practice:'))
        if (first){
          const parts = (first.item_id||'').split(':')
          if (parts.length >= 3){
            const dif = parts[1]
            if (['easy','medium','hard'].includes(dif)) this.difficulty = dif
          }
        }
      }catch(_){ /* ignore for guests */ }
      finally{ this.loadingDue = false }
    },
    explain(mode){
      const p = this.paragraph.trim()
      if (!p) { this.explanation = 'أدخل فقرة أولاً.'; return }
      if (mode==='simple') this.explanation = `ببساطة: ${p.replace(/\b(\w{8,})\b/g,'$1').split('.').slice(0,3).join('. ')}.`
      else if (mode==='analogy') this.explanation = `تخيل أن ${p.split(' ').slice(0,12).join(' ')}... مثل ملعب كرة بين عناصر تتعاون.`
      else this.explanation = `الخطوات: 1) ${p.split('.').filter(Boolean).map(s=>s.trim())[0] || 'اقرأ الفقرة'}  2) حدد المفاهيم  3) طبّق على مثال  4) راجع الناتج`
    }
  },
  async created(){
    await this.loadDue()
  }
}
</script>

<style scoped>
</style>
