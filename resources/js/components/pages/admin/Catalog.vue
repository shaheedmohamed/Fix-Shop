<template>
  <div class="container py-4">
    <h2 class="fw-bold mb-3">Catalog Manager</h2>
    <div class="row g-4">
      <!-- Add Category -->
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-primary text-white">Add Category</div>
          <div class="card-body">
            <div class="mb-2">
              <label class="form-label small">Category name</label>
              <input v-model="catName" type="text" class="form-control" placeholder="e.g., Electronics" @keyup.enter="addCategory">
            </div>
            <button class="btn btn-primary w-100" :disabled="savingCat || !catName" @click="addCategory">
              <span v-if="!savingCat">Add</span>
              <span v-else class="spinner-border spinner-border-sm"></span>
            </button>
          </div>
        </div>
      </div>

      <!-- Add Subcategory -->
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-primary text-white">Add Subcategory</div>
          <div class="card-body">
            <div class="mb-2">
              <label class="form-label small">Parent category</label>
              <select v-model="subCatId" class="form-select">
                <option :value="null">Select category</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label small">Subcategory name</label>
              <input v-model="subName" type="text" class="form-control" placeholder="e.g., Smartwatches" @keyup.enter="addSubcategory">
            </div>
            <button class="btn btn-primary w-100" :disabled="savingSub || !subName || !subCatId" @click="addSubcategory">
              <span v-if="!savingSub">Add</span>
              <span v-else class="spinner-border spinner-border-sm"></span>
            </button>
          </div>
        </div>
      </div>

      <!-- Listing -->
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-light">Categories</div>
          <div class="card-body p-0">
            <div v-if="loading" class="text-center py-4"><div class="spinner-border"></div></div>
            <div v-else class="list-group list-group-flush small">
              <div v-for="c in categories" :key="c.id" class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>{{ c.name }}</strong>
                  <button class="btn btn-sm btn-outline-danger" @click="removeCategory(c)"><i class="fa-solid fa-trash"></i></button>
                </div>
                <div class="mt-2 d-flex flex-wrap gap-1">
                  <span v-for="s in c.subcategories" :key="s.id" class="badge text-bg-secondary d-flex align-items-center gap-1">
                    {{ s.name }}
                    <button class="btn btn-xs btn-link text-white p-0" title="Remove" @click="removeSub(s)"><i class="fa-solid fa-xmark"></i></button>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'AdminCatalog',
  data(){
    return {
      categories: [],
      loading: false,
      catName: '',
      subName: '',
      subCatId: null,
      savingCat: false,
      savingSub: false,
    }
  },
  methods: {
    async load(){
      this.loading = true
      try{
        const { data } = await axios.get('/api/catalog/categories')
        this.categories = data || []
      } finally { this.loading = false }
    },
    async addCategory(){
      if (!this.catName) return
      this.savingCat = true
      try{
        await axios.post('/api/catalog/categories', { name: this.catName })
        this.catName = ''
        await this.load()
      } finally { this.savingCat = false }
    },
    async addSubcategory(){
      if (!this.subName || !this.subCatId) return
      this.savingSub = true
      try{
        await axios.post('/api/catalog/subcategories', { name: this.subName, category_id: this.subCatId })
        this.subName = ''
        await this.load()
      } finally { this.savingSub = false }
    },
    async removeCategory(c){
      if (!confirm('Delete category and its subcategories?')) return
      await axios.delete(`/api/catalog/categories/${c.id}`)
      await this.load()
    },
    async removeSub(s){
      if (!confirm('Delete subcategory?')) return
      await axios.delete(`/api/catalog/subcategories/${s.id}`)
      await this.load()
    }
  },
  mounted(){ this.load() }
}
</script>
