<template>
  <div class="card product-card h-100">
    <div class="position-relative">
      <img :src="imageUrl(product.image_path)" class="card-img-top" alt="product" />
    </div>
    <div class="card-body">
      <a class="small text-success text-decoration-none" href="#" v-if="product.colors_count">+{{ product.colors_count }} other colors/patterns</a>
      <h6 class="product-title mt-1">
        <RouterLink class="stretched-link text-decoration-none text-dark" :to="{ name: 'product', params: { id: product.id } }">
          {{ product.name }}
        </RouterLink>
      </h6>
      <div class="d-flex align-items-center gap-1 small mb-1">
        <i class="fa-solid fa-star text-warning"></i>
        <i class="fa-solid fa-star text-warning"></i>
        <i class="fa-solid fa-star text-warning"></i>
        <i class="fa-solid fa-star-half-stroke text-warning"></i>
        <i class="fa-regular fa-star text-warning"></i>
        <span class="text-muted">({{ product.reviews_count || 0 }})</span>
      </div>
      <div class="h5 mb-1">EGP {{ formatPrice(product.price_discount || product.price) }}</div>
      <div v-if="product.free_delivery" class="text-success small">FREE delivery {{ deliveryDate }}</div>
      <button class="btn btn-warning mt-2 w-100" @click.stop="addToCart">
        <i class="fa-solid fa-cart-plus me-2"></i>Add to cart
      </button>
    </div>
  </div>
</template>

<script>
import cart from '../../store/cart'
export default {
  name: 'ProductCard',
  props: { product: { type: Object, required: true } },
  computed: {
    deliveryDate(){
      const d = new Date(); d.setDate(d.getDate()+3); return d.toLocaleDateString()
    }
  },
  methods: {
    imageUrl(p){ return p?.startsWith('http') ? p : `/storage/${p}` },
    formatPrice(v){ try{ return Number(v).toLocaleString('en-EG') }catch{ return v } },
    addToCart(){ cart.add(this.product) },
  }
}
</script>

<style scoped>
.card-img-top{ height: 220px; object-fit: cover; }
.product-title{ min-height: 2.6em; }
</style>
