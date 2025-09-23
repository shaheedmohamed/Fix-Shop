<template>
  <div class="container py-4">
    <h2 class="mb-3">Products</h2>
    <div class="row g-3 product-grid">
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
          <button class="add-btn" @click="addToCart(p)"><i class="fa-solid fa-cart-shopping me-2"></i> Add to cart</button>
          <div class="added-banner" v-if="added.has(p.id)">Added!</div>
        </div>
      </div>
      <div v-if="!loading && items.length===0" class="text-muted">No products yet.</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import cart from '../../store/cart'
export default {
  name: 'ProductsPublicPage',
  data(){ return { items: [], loading: false, added: new Set() } },
  methods: {
    imageUrl(p){
      if (!p) return ''
      if (typeof p !== 'string') return ''
      if (p.startsWith('http')) return p
      if (p.startsWith('/storage/')) return p
      return `/storage/${p}`
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
    async load(){
      this.loading = true
      try{ const { data } = await axios.get('/api/products'); this.items = data?.data || data || [] }
      catch(e){ /* ignore */ }
      finally{ this.loading = false }
    }
  },
  mounted(){ this.load() }
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
