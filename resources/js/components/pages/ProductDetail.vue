<template>
  <div class="container py-4" v-if="product">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><RouterLink to="/">Home</RouterLink></li>
        <li class="breadcrumb-item"><RouterLink to="/products">Products</RouterLink></li>
        <li class="breadcrumb-item active">{{ product.name }}</li>
      </ol>
    </nav>

    <div class="row g-4">
      <!-- Product Images -->
      <div class="col-md-6">
        <div class="product-gallery">
          <div class="main-image mb-3">
            <img :src="imageUrl(product.image_path)" :alt="product.name" class="img-fluid rounded shadow-sm" />
          </div>
        </div>
      </div>

      <!-- Product Info -->
      <div class="col-md-6">
        <div class="product-info">
          <h1 class="product-title mb-3">{{ product.name }}</h1>
          
          <div class="price-section mb-4">
            <div class="current-price">
              <span class="price-amount">EGP {{ displayPrice(product).toLocaleString() }}</span>
              <span v-if="oldPrice(product)" class="old-price ms-2">EGP {{ oldPrice(product).toLocaleString() }}</span>
            </div>
          </div>

          <div class="product-meta mb-4">
            <div class="meta-item" v-if="product.brand">
              <strong>Brand:</strong> {{ product.brand }}
            </div>
            <div class="meta-item" v-if="product.sku">
              <strong>SKU:</strong> {{ product.sku }}
            </div>
            <div class="meta-item" v-if="product.stock_qty !== null">
              <strong>Stock:</strong> {{ product.stock_qty }} available
            </div>
          </div>

          <div class="quantity-section mb-4">
            <label class="form-label">Quantity:</label>
            <div class="quantity-controls d-flex align-items-center gap-2">
              <button class="btn btn-outline-secondary btn-sm" @click="decreaseQty" :disabled="qty <= 1">-</button>
              <input v-model.number="qty" type="number" min="1" class="form-control text-center" style="width: 80px;">
              <button class="btn btn-outline-secondary btn-sm" @click="increaseQty">+</button>
            </div>
          </div>

          <div class="action-buttons mb-4">
            <button class="btn btn-primary btn-lg me-3" @click="addToCart" :disabled="adding">
              <i class="fa-solid fa-cart-shopping me-2"></i>
              {{ adding ? 'Adding...' : 'Add to Cart' }}
            </button>
            <button class="btn btn-outline-primary btn-lg" @click="chatWithSeller" :disabled="chatting">
              <i class="fa-regular fa-comments me-2"></i>
              {{ chatting ? 'Starting chat...' : 'Chat with seller' }}
            </button>
          </div>

          <div class="product-description" v-if="product.description">
            <h5>Description</h5>
            <p>{{ product.description }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Related Products -->
    <div class="related-products mt-5" v-if="relatedProducts.length > 0">
      <h4 class="mb-4">Related Products</h4>
      <div class="row g-3">
        <div v-for="p in relatedProducts" :key="p.id" class="col-sm-6 col-md-3">
          <div class="sp-card h-100">
            <div class="thumb-wrap">
              <img v-if="p.image_path" :src="imageUrl(p.image_path)" :alt="p.name" />
            </div>
            <div class="sp-body">
              <div class="name">{{ p.name }}</div>
              <div class="prices">
                <span class="price">{{ displayPrice(p).toLocaleString() }} DT</span>
                <span v-if="oldPrice(p)" class="old">{{ oldPrice(p).toLocaleString() }} DT</span>
              </div>
            </div>
            <button class="add-btn" @click="addRelatedToCart(p)">
              <i class="fa-solid fa-cart-shopping me-2"></i> Add to cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div v-else-if="loading" class="container py-5 text-center">
    <div class="spinner-border" role="status"></div>
    <div class="mt-2">Loading product...</div>
  </div>
  
  <div v-else class="container py-5 text-center">
    <h3>Product not found</h3>
    <RouterLink to="/products" class="btn btn-primary mt-3">Back to Products</RouterLink>
  </div>
</template>

<script>
import axios from 'axios'
import auth from '../../store/auth'
import cart from '../../store/cart'

export default {
  name: 'ProductDetailPage',
  props: ['id'],
  data() {
    return {
      product: null,
      relatedProducts: [],
      qty: 1,
      adding: false,
      loading: true,
      chatting: false
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
    async chatWithSeller(){
      if(!auth.isAuthenticated()){ 
        this.$router.push({ name: 'login', query: { redirect: this.$route.fullPath } }); 
        return 
      }
      try{
        this.chatting = true
        const { data } = await axios.post('/api/chat/start', { seller_id: this.product.user_id, product_id: this.product.id })
        // Redirect to a simple chat interface instead of messages
        this.$router.push({ name: 'chat-conversation', params: { id: data.id } })
      }catch(e){ 
        console.error('Chat error:', e)
        alert(e?.response?.data?.message || 'Failed to start chat') 
      }
      finally{ this.chatting = false }
    },
    displayPrice(p) {
      const price = Number(p.price || 0)
      const discount = Number(p.price_discount || 0)
      if (discount && discount > 0 && discount < price) return discount
      return price
    },
    oldPrice(p) {
      const price = Number(p.price || 0)
      const discount = Number(p.price_discount || 0)
      if (discount && discount > 0 && discount < price) return price
      return null
    },
    increaseQty() {
      this.qty++
    },
    decreaseQty() {
      if (this.qty > 1) this.qty--
    },
    async addToCart() {
      this.adding = true
      try {
        for (let i = 0; i < this.qty; i++) {
          cart.add({
            id: this.product.id,
            name: this.product.name,
            price: this.displayPrice(this.product),
            image: this.imageUrl(this.product.image_path)
          })
        }
        cart.toast(`Added ${this.qty} item(s) to cart!`)
      } finally {
        this.adding = false
      }
    },
    addRelatedToCart(p) {
      cart.add({
        id: p.id,
        name: p.name,
        price: this.displayPrice(p),
        image: this.imageUrl(p.image_path)
      })
      cart.toast('Added to cart!')
    },
    async loadProduct() {
      this.loading = true
      try {
        const { data } = await axios.get(`/api/products/${this.id}`)
        this.product = data
        // Load related products (same category)
        if (data.category_id) {
          const related = await axios.get(`/api/products?category=${data.category_id}&limit=4&exclude=${this.id}`)
          this.relatedProducts = related.data?.data || []
        }
      } catch (e) {
        console.error('Failed to load product:', e)
      } finally {
        this.loading = false
      }
    }
  },
  async mounted() {
    await this.loadProduct()
  },
  watch: {
    id() {
      this.loadProduct()
    }
  }
}
</script>

<style scoped>
.product-gallery .main-image img {
  width: 100%;
  max-height: 500px;
  object-fit: contain;
}

.product-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1a1a1a;
}

.price-section .current-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #ff7a00;
}

.old-price {
  font-size: 1.2rem;
  color: #999;
  text-decoration: line-through;
}

.meta-item {
  margin-bottom: 0.5rem;
  color: #666;
}

.quantity-controls input {
  max-width: 80px;
}

.sp-card {
  border: 2px solid rgba(255,122,0,.35);
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  display: flex;
  flex-direction: column;
}

.thumb-wrap {
  height: 180px;
  display: grid;
  place-items: center;
  padding: 10px;
}

.thumb-wrap img {
  max-height: 100%;
  max-width: 100%;
  object-fit: contain;
}

.sp-body {
  padding: 10px 12px;
}

.sp-body .name {
  font-weight: 600;
  color: #0f172a;
  line-height: 1.2;
  height: 44px;
  overflow: hidden;
}

.prices {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-top: 6px;
}

.prices .price {
  color: #ff7a00;
  font-weight: 800;
}

.prices .old {
  color: #94a3b8;
  text-decoration: line-through;
  font-size: .9rem;
}

.add-btn {
  background: #ff7a00;
  color: #fff;
  border: 0;
  padding: 10px;
  font-weight: 700;
}

.add-btn:hover {
  filter: brightness(.95);
}
</style>
