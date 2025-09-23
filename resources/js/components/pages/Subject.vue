<template>
  <div class="min-h-screen bg-gray-50">
    <section class="container py-5">
      <div class="bg-white rounded-3 shadow-sm p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h3 fw-bold mb-0">{{ subjectData?.name || title }}</h1>
          <RouterLink :to="{ name: 'home' }" class="btn btn-outline-secondary">Back to Home</RouterLink>
        </div>
        <p v-if="subjectData?.description" class="text-muted mb-4">{{ subjectData.description }}</p>

        <div v-if="loading" class="text-center py-4">
          <span class="spinner-border"></span>
        </div>
        <div v-else>
          <div v-if="books.length" class="row g-4">
            <div class="col-md-6 col-lg-4" v-for="b in books" :key="b.id">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title">{{ b.title }}</h5>
                  <p class="text-muted small mb-1" v-if="b.author">By {{ b.author }}</p>
                  <p class="text-muted small flex-grow-1" v-if="b.description">{{ b.description }}</p>
                  <a :href="bookUrl(b)" target="_blank" class="btn btn-primary btn-sm mt-2">View / Download PDF</a>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-muted py-4">No books added yet for this subject.</div>
        </div>
      </div>
    </section>
    <Footer />
  </div>
  
</template>

<script>
import Footer from '../layouts/Footer.vue'
import axios from 'axios'
export default {
  name: 'SubjectPage',
  components: { Footer },
  data(){
    return { subjectData: null, books: [], loading: true }
  },
  computed: {
    subject(){ return this.$route.params.subject },
    title(){ return this.subject.charAt(0).toUpperCase() + this.subject.slice(1) }
  },
  async created(){
    await this.fetch()
  },
  watch: {
    '$route.params.subject': async function(){ await this.fetch() }
  },
  methods: {
    async fetch(){
      this.loading = true
      try{
        const { data } = await axios.get(`/api/subjects/${this.subject}`)
        this.subjectData = data
        this.books = data.books || []
      } catch(e){
        this.subjectData = { name: this.title }
        this.books = []
      } finally{ this.loading = false }
    },
    bookUrl(b){
      // Prefer direct storage URL if symlink exists; otherwise fallback to streaming route
      return b.file_url && b.file_url.startsWith('/storage') ? b.file_url : `/books/${b.id}/file`
    }
  }
}
</script>
