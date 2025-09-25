<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="m-0">My Products</h3>
      <RouterLink class="btn btn-primary" :to="{ name: 'vendor-product-new' }">
        <i class="fa-solid fa-plus me-2"></i>Add New Product
      </RouterLink>
    </div>

    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center py-4">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">Loading products...</p>
        </div>

        <div v-else-if="products.length === 0" class="text-center py-5">
          <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
          <h5 class="text-muted">No products yet</h5>
          <p class="text-muted">Start by adding your first product</p>
          <RouterLink class="btn btn-primary" :to="{ name: 'vendor-product-new' }">
            <i class="fa-solid fa-plus me-2"></i>Add Product
          </RouterLink>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 80px;">Image</th>
                <th>Product Name</th>
                <th style="width: 120px;">Price</th>
                <th style="width: 100px;">Stock</th>
                <th style="width: 120px;">Created</th>
                <th style="width: 150px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in products" :key="p.id">
                <td>
                  <div class="product-image-cell">
                    <img v-if="p.image_path" :src="imageUrl(p.image_path)" :alt="p.name" class="product-thumbnail" />
                    <div v-else class="product-placeholder">
                      <i class="fa-regular fa-image"></i>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="product-info">
                    <h6 class="mb-1 fw-bold">{{ p.name }}</h6>
                    <small class="text-muted">{{ p.description || 'No description' }}</small>
                  </div>
                </td>
                <td>
                  <div class="price-cell">
                    <span class="fw-bold text-success">EGP {{ Number(p.price).toLocaleString() }}</span>
                    <div v-if="p.price_discount" class="text-muted small">
                      <del>EGP {{ Number(p.price_discount).toLocaleString() }}</del>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge" :class="getStockBadge(p.stock_qty)">
                    {{ p.stock_qty || 0 }}
                  </span>
                </td>
                <td>
                  <small class="text-muted">{{ formatDate(p.created_at) }}</small>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <RouterLink :to="{ name: 'vendor-product-edit', params: { id: p.id } }" 
                                class="btn btn-sm btn-outline-primary" title="Edit Product">
                      <i class="fa-solid fa-edit"></i>
                    </RouterLink>
                    <RouterLink :to="{ name: 'product-detail', params: { id: p.id } }" 
                                class="btn btn-sm btn-outline-info" title="View Product" target="_blank">
                      <i class="fa-solid fa-eye"></i>
                    </RouterLink>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(p)" title="Delete Product">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
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
      if (!p) return '/images/placeholder-product.svg'
      if (typeof p !== 'string') return '/images/placeholder-product.svg'
      if (p.startsWith('http')) return p
      if (p.startsWith('/storage/')) return p
      // Fix for Laravel storage path
      if (p.startsWith('img/')) return `/storage/${p}`
      return `/storage/img/${p}`
    },
    async load(){
      this.loading = true
      try{ const { data } = await axios.get('/api/vendor/products'); this.products = data || [] }
      catch(e){ /* ignore */ }
      finally{ this.loading = false }
    },
    async remove(p){
      if(!confirm(`Are you sure you want to delete "${p.name}"? This action cannot be undone.`)) return
      try{ 
        await axios.delete(`/api/vendor/products/${p.id}`)
        this.products = this.products.filter(x=>x.id!==p.id)
        alert('Product deleted successfully!')
      }
      catch(e){ 
        console.error('Failed to delete:', e)
        alert('Failed to delete product: ' + (e.response?.data?.message || e.message))
      }
    },

    getStockBadge(stock) {
      if (!stock || stock === 0) return 'bg-danger'
      if (stock < 10) return 'bg-warning'
      return 'bg-success'
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }
  },
  mounted(){ this.load() }
}
</script>

<style scoped>
.product-thumbnail {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.product-placeholder {
  width: 50px;
  height: 50px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
}

.product-info h6 {
  color: #374151;
  line-height: 1.2;
}

.product-info small {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.3;
}

.price-cell {
  white-space: nowrap;
}

.table th {
  font-weight: 600;
  color: #374151;
  border-bottom: 2px solid #e5e7eb;
}

.table td {
  vertical-align: middle;
  border-bottom: 1px solid #f3f4f6;
}

.table tbody tr:hover {
  background-color: #f9fafb;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.card {
  border: none;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
</style>
