<template>
  <div class="container py-4">
    <div class="row g-4">
      <!-- Sidebar Filters -->
      <div class="col-lg-3">
        <ShopFilters :categories="categories" :loading-cats="loadingCats" @change="onFiltersChange" />
      </div>

      <!-- Products Grid -->
      <div class="col-lg-9">
        <div class="d-flex align-items-baseline justify-content-between mb-2">
          <h2 class="mb-0">Products</h2>
          <small class="text-muted">{{ items.length }} results</small>
        </div>

        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
        </div>
        <div v-else class="row g-3 product-grid">
          <div v-for="p in items" :key="p.id" class="col-sm-6 col-md-4 col-lg-3">
            <div class="sp-card h-100">
              <div class="thumb-wrap">
                <img v-if="p.image_path" :src="imageUrl(p.image_path)" :alt="p.name" />
              </div>
              <div class="sp-body">
                <div class="name">{{ p.name }}</div>
                <div class="prices">
                  <span class="price">EGP {{ displayPrice(p).toLocaleString(undefined,{minimumFractionDigits:0}) }}</span>
                  <span v-if="oldPrice(p)" class="old">EGP {{ oldPrice(p).toLocaleString(undefined,{minimumFractionDigits:0}) }}</span>
                </div>
              </div>
              <RouterLink :to="{ name: 'product-detail', params: { id: p.id } }" class="add-btn text-decoration-none text-white d-block text-center">
                <i class="fa-solid fa-eye me-2"></i> View Details
              </RouterLink>
              <div class="added-banner" v-if="added.has(p.id)">Added!</div>
            </div>
          </div>
          <div v-if="!loading && items.length===0" class="text-muted">No products found.</div>
        </div>
        <div class="text-center mt-3" v-if="hasMore">
          <button class="btn btn-outline-primary" @click="loadMore" :disabled="loading">
            <span v-if="!loading">Load more</span>
            <span v-else>Loading...</span>
          </button>
        </div>
      </div>
    </div>
  </div>
  </template>

<script>
import axios from 'axios'
import cart from '../../store/cart'
import ShopFilters from '../shop/Filters.vue'
export default {
  name: 'ProductsPublicPage',
  components: { ShopFilters },
  data(){ 
    return { 
      items: [], 
      loading: false, 
      added: new Set(),
      // filters state
      categories: [],
      loadingCats: false,
      filters: { category_id: null, min: null, max: null },
      // pagination
      page: 1,
      limit: 50,
      hasMore: false,
    }
  },
  methods: {
    imageUrl(p) {
      if (!p) return '/images/placeholder-product.svg'
      if (typeof p !== 'string') return '/images/placeholder-product.svg'
      if (p.startsWith('http')) return p
      if (p.startsWith('/storage/')) return p
      // Fix for Laravel storage path
      if (p.startsWith('img/')) return `/storage/${p}`
      return `/storage/img/${p}`
    },
    displayPrice(p){
      const price = Number(p.price || 0)
      const discount = Number(p.price_discount || 0)
      if (discount && discount > 0 && discount < price) return discount
      return price
    },
    oldPrice(p){
      const price = Number(p.price || 0)
      const discount = Number(p.price_discount || 0)
      if (discount && discount > 0 && discount < price) return price
      if (discount && discount > price) return discount
      return null
    },
    addToCart(p){
      cart.add({
        id: p.id,
        name: p.name,
        price: this.displayPrice(p),
        image: this.imageUrl(p.image_path)
      })
      cart.toast('Added to cart!')
      this.added.add(p.id)
      setTimeout(()=> this.added.delete(p.id), 1200)
    },
    async load(append=false){
      this.loading = true
      try{ 
        // Build params from route query
        const params = { page: this.page, limit: this.limit }
        const q = this.$route?.query || {}
        if (q.category) params.category = q.category
        if (q.subcategory) params.subcategory = q.subcategory
        if (q.min) params.min = q.min
        if (q.max) params.max = q.max
        const { data } = await axios.get('/api/products', { params })
        const list = data?.data || data || []
        this.hasMore = !!(data?.has_more)
        this.items = append ? [...this.items, ...list] : list
      }
      catch(e){ 
        console.error('Failed to load products:', e)
        alert('Failed to load products: ' + e.message)
      }
      finally{ this.loading = false }
    },
    loadMore(){
      if (this.hasMore && !this.loading) {
        this.page += 1
        this.load(true)
      }
    },
    async fetchCategories(){
      this.loadingCats = true
      try{
        const { data } = await axios.get('/api/catalog/categories')
        this.categories = data || []
      } finally { this.loadingCats = false }
    },
    onFiltersChange(f){
      this.filters = { ...this.filters, ...f }
      const q = { ...this.$route.query }
      q.category = this.filters.category_id || undefined
      q.min = this.filters.min || undefined
      q.max = this.filters.max || undefined
      this.$router.push({ query: q })
    },
  },
  mounted(){ 
    // init local filters from route
    const q = this.$route?.query || {}
    this.filters.category_id = q.category ? Number(q.category) : null
    this.filters.min = q.min ? Number(q.min) : null
    this.filters.max = q.max ? Number(q.max) : null
    this.fetchCategories()
    this.page = 1
    this.load() 
  },
  watch: {
    '$route.query': {
      handler(){ this.page = 1; this.load() },
      deep: true
    }
  }
}
</script>

<style scoped>
.product-grid { --accent: #ff7a00; }
.sp-card { border: 2px solid rgba(255,122,0,.35); border-radius: 12px; overflow: hidden; background: #fff; display: flex; flex-direction: column; }
.thumb-wrap { height: 180px; display: grid; place-items: center; padding: 10px; }
.thumb-wrap img { max-height: 100%; max-width: 100%; object-fit: contain; }
.sp-body { padding: 10px 12px; }
.sp-body .name { font-weight: 600; color: #0f172a; line-height: 1.2; height: 44px; overflow: hidden; }
.prices { display: flex; align-items: baseline; gap: 8px; margin-top: 6px; }
.prices .price { color: var(--accent); font-weight: 800; }
.prices .old { color: #94a3b8; text-decoration: line-through; font-size: .9rem; }
.add-btn { background: var(--accent); color: #fff; border: 0; padding: 10px; font-weight: 700; }
.add-btn:hover { filter: brightness(.95); }
</style>
