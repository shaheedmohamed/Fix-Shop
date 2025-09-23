<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">My Products</h3>
      <RouterLink class="btn btn-primary" :to="{ name: 'vendor-product-new' }"><i class="fa-solid fa-plus me-2"></i>Add New</RouterLink>
    </div>
    <div class="row g-3">
      <div v-for="p in products" :key="p.id" class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img v-if="p.image_path" class="card-img-top" :src="imageUrl(p.image_path)" :alt="p.name" />
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ p.name }}</h5>
            <div class="text-muted small mb-2">EGP {{ Number(p.price).toFixed(2) }}</div>
            <p class="card-text flex-grow-1">{{ p.description || 'No description' }}</p>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-danger btn-sm" @click="remove(p)"><i class="fa-regular fa-trash-can me-1"></i>Delete</button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="!loading && products.length===0" class="text-muted">No products yet.</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'VendorProductsPage',
  data(){ return { products: [], loading: false } },
  methods:{
    imageUrl(p){
      if (!p) return ''
      if (typeof p !== 'string') return ''
      if (p.startsWith('http')) return p
      if (p.startsWith('/storage/')) return p
      return `/storage/${p}`
    },
    async load(){
      this.loading = true
      try{ const { data } = await axios.get('/api/vendor/products'); this.products = data || [] }
      catch(e){ /* ignore */ }
      finally{ this.loading = false }
    },
    async remove(p){
      if(!confirm('Delete product?')) return
      try{ await axios.delete(`/api/vendor/products/${p.id}`); this.products = this.products.filter(x=>x.id!==p.id) }
      catch(e){ alert('Failed to delete') }
    }
  },
  mounted(){ this.load() }
}
</script>
