<template>
  <div class="min-vh-100 bg-gray-50">
    <section class="container py-5">
      <div class="bg-white rounded-3 shadow-sm p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h1 class="h3 fw-bold mb-0">All Subjects</h1>
          <RouterLink :to="{ name: 'home' }" class="btn btn-outline-secondary">Back to Home</RouterLink>
        </div>

        <div v-if="loading" class="text-center py-4"><span class="spinner-border"></span></div>
        <div v-else>
          <div v-if="subjects.length" class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-3">
            <div class="col" v-for="s in subjects" :key="s.id">
              <RouterLink :to="{ name: 'subject', params: { subject: s.slug } }" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm card-hover">
                  <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="display-6 text-primary"><i class="fa-solid fa-book-open"></i></div>
                    <div>
                      <h5 class="fw-semibold mb-1">{{ s.name }}</h5>
                      <p class="text-muted small mb-0">{{ s.description || 'Browse books in this subject' }}</p>
                    </div>
                  </div>
                </div>
              </RouterLink>
            </div>
          </div>
          <div v-else class="text-center text-muted py-4">No subjects found.</div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'SubjectsPage',
  data(){ return { subjects: [], loading: true } },
  async created(){
    this.loading = true
    try {
      const { data } = await axios.get('/api/subjects')
      this.subjects = Array.isArray(data) ? data : (data.data || [])
    } catch (_) { this.subjects = [] }
    finally { this.loading = false }
  }
}
</script>

<style scoped>
.card-hover{ transition: transform .2s ease, box-shadow .2s ease; }
.card-hover:hover{ transform: translateY(-2px); box-shadow: 0 10px 24px rgba(2,6,23,.06); }
</style>
