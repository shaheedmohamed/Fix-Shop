<template>
  <section class="container py-4">
    <div v-if="recentCategoryProducts.length > 0" class="mb-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="m-0">Based on your favorite category</h5>
        <RouterLink v-if="recentCategoryId" :to="{ name: 'products', query: { category: recentCategoryId } }" class="small">See more</RouterLink>
      </div>
      <div class="row g-3 flex-nowrap overflow-auto no-scrollbar">
        <div v-for="p in recentCategoryProducts" :key="p.id" class="col-8 col-sm-4 col-md-3 col-lg-2">
          <RouterLink class="rec-card text-decoration-none" :to="{ name: 'product-detail', params: { id: p.id } }">
            <div class="thumb"><img :src="imageUrl(p.image_path)" :alt="p.name"></div>
            <div class="name">{{ p.name }}</div>
            <div class="price">EGP {{ displayPrice(p).toLocaleString() }}</div>
          </RouterLink>
        </div>
      </div>
    </div>

    <div v-if="popularProducts.length > 0" class="mb-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="m-0">Most Viewed</h5>
        <RouterLink :to="{ name: 'products' }" class="small">See more</RouterLink>
      </div>
      <div class="row g-3 flex-nowrap overflow-auto no-scrollbar">
        <div v-for="p in popularProducts" :key="p.id" class="col-8 col-sm-4 col-md-3 col-lg-2">
          <RouterLink class="rec-card text-decoration-none" :to="{ name: 'product-detail', params: { id: p.id } }">
            <div class="thumb"><img :src="imageUrl(p.image_path)" :alt="p.name"></div>
            <div class="name">{{ p.name }}</div>
            <div class="price">EGP {{ displayPrice(p).toLocaleString() }}</div>
          </RouterLink>
        </div>
      </div>
    </div>

    <div v-if="searchBasedProducts.length > 0">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="m-0">Popular from your searches</h5>
        <RouterLink :to="{ name: 'products' }" class="small">See more</RouterLink>
      </div>
      <div class="row g-3 flex-nowrap overflow-auto no-scrollbar">
        <div v-for="p in searchBasedProducts" :key="p.id" class="col-8 col-sm-4 col-md-3 col-lg-2">
          <RouterLink class="rec-card text-decoration-none" :to="{ name: 'product-detail', params: { id: p.id } }">
            <div class="thumb"><img :src="imageUrl(p.image_path)" :alt="p.name"></div>
            <div class="name">{{ p.name }}</div>
            <div class="price">EGP {{ displayPrice(p).toLocaleString() }}</div>
          </RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios'
export default {
  name: 'HomeRecommendations',
  data(){
    return {
      recentCategoryId: null,
      recentCategoryProducts: [],
      popularProducts: [],
      searchBasedProducts: [],
      loading: false,
    }
  },
  methods: {
    displayPrice(p){
      const price = Number(p.price || 0)
      const discount = Number(p.price_discount || 0)
      return (discount && discount > 0 && discount < price) ? discount : price
    },
    imageUrl(p){
      if (!p) return '/images/placeholder-product.svg'
      if (typeof p !== 'string') return '/images/placeholder-product.svg'
      if (p.startsWith('http')) return p
      if (p.startsWith('/storage/')) return p
      if (p.startsWith('img/')) return `/storage/${p}`
      return `/storage/img/${p}`
    },
    getTopCategoryFromStorage(){
      try{
        const raw = localStorage.getItem('cat_hits')
        if (!raw) return null
        const obj = JSON.parse(raw)
        const entries = Object.entries(obj || {})
        if (entries.length===0) return null
        entries.sort((a,b)=> b[1]-a[1])
        return Number(entries[0][0])
      } catch{ return null }
    },
    getTopSearchTerm(){
      try{
        const raw = localStorage.getItem('search_hits')
        const obj = JSON.parse(raw || '{}')
        const entries = Object.entries(obj)
        if (entries.length===0) return null
        entries.sort((a,b)=> b[1]-a[1])
        return entries[0][0]
      } catch{ return null }
    },
    async fetchData(){
      this.loading = true
      try{
        // Recent category based
        this.recentCategoryId = this.getTopCategoryFromStorage()
        if (this.recentCategoryId){
          const { data } = await axios.get('/api/products', { params: { category: this.recentCategoryId, limit: 12 } })
          this.recentCategoryProducts = data?.data || data || []
        }
        // Popular: fallback to latest products for now
        const pop = await axios.get('/api/products', { params: { limit: 12 } })
        this.popularProducts = pop.data?.data || pop.data || []
        // Search based: if we ever store search terms; for now reuse popular
        const topTerm = this.getTopSearchTerm()
        if (topTerm) {
          // If you later add a search API, call it here
          const s = await axios.get('/api/products', { params: { limit: 12 } })
          this.searchBasedProducts = s.data?.data || s.data || []
        } else {
          this.searchBasedProducts = this.popularProducts.slice(0,12)
        }
      } finally { this.loading = false }
    }
  },
  mounted(){ this.fetchData() }
}
</script>

<style scoped>
.no-scrollbar { scrollbar-width: none; }
.no-scrollbar::-webkit-scrollbar { display: none; }
.rec-card { display:block; border: 1px solid #e9ecef; border-radius: 10px; overflow: hidden; background:#fff; }
.rec-card .thumb{ height: 140px; display:grid; place-items:center; background:#fff; }
.rec-card .thumb img{ max-height: 100%; max-width: 100%; object-fit: contain; }
.rec-card .name{ color:#0f172a; font-weight:600; padding:6px 8px 2px; height:38px; overflow:hidden; }
.rec-card .price{ color:#ff7a00; font-weight:800; padding:0 8px 10px; }
</style>
