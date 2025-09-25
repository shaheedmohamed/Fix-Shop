<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">All Products</h3>
      <button class="btn btn-success" @click="liveDemo">
        <i class="fa-solid fa-play me-2"></i>Live Demo
      </button>
    </div>
    
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Seller</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products" :key="product.id">
                <td>
                  <img v-if="product.image_path" :src="imageUrl(product.image_path)" 
                       class="rounded" style="width: 50px; height: 50px; object-fit: cover;" />
                  <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" 
                       style="width: 50px; height: 50px;">
                    <i class="fa-regular fa-image text-muted"></i>
                  </div>
                </td>
                <td>{{ product.name }}</td>
                <td>EGP {{ Number(product.price).toLocaleString() }}</td>
                <td>{{ product.user?.store_name || product.user?.name || 'N/A' }}</td>
                <td>{{ new Date(product.created_at).toLocaleDateString() }}</td>
                <td>
                  <div class="d-flex gap-1">
                    <RouterLink :to="{ name: 'product-detail', params: { id: product.id } }" 
                                class="btn btn-sm btn-outline-info" target="_blank">
                      <i class="fa-solid fa-eye me-1"></i>View
                    </RouterLink>
                    <RouterLink :to="{ name: 'admin-product-edit', params: { id: product.id } }" 
                                class="btn btn-sm btn-outline-primary">
                      <i class="fa-solid fa-edit me-1"></i>Edit
                    </RouterLink>
                    <button class="btn btn-sm btn-outline-danger" @click="deleteProduct(product)" :disabled="deleting === product.id">
                      <i class="fa-solid fa-trash me-1"></i>{{ deleting === product.id ? 'Deleting...' : 'Delete' }}
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!loading && products.length === 0">
                <td colspan="6" class="text-center text-muted">No products found</td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div v-if="loading" class="text-center py-3">
          <div class="spinner-border" role="status"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AdminProductsPage',
  data() {
    return {
      products: [],
      loading: false,
      deleting: null
    }
  },
  methods: {
    imageUrl(path) {
      if (!path) return '/images/placeholder-product.svg'
      if (path.startsWith('http')) return path
      if (path.startsWith('/storage/')) return path
      // Fix for Laravel storage path
      if (path.startsWith('img/')) return `/storage/${path}`
      return `/storage/img/${path}`
    },
    async loadProducts() {
      this.loading = true
      try {
        const { data } = await axios.get('/api/admin/products')
        this.products = data?.data || data || []
      } catch (e) {
        console.error('Failed to load products:', e)
      } finally {
        this.loading = false
      }
    },
    liveDemo() {
      window.open('/products', '_blank')
    },

    async deleteProduct(product) {
      if (!confirm(`Are you sure you want to delete "${product.name}"? This action cannot be undone.`)) {
        return
      }

      this.deleting = product.id
      try {
        await axios.delete(`/api/admin/products/${product.id}`)
        this.products = this.products.filter(p => p.id !== product.id)
        alert('Product deleted successfully!')
      } catch (e) {
        console.error('Failed to delete product:', e)
        alert('Failed to delete product: ' + (e.response?.data?.message || e.message))
      } finally {
        this.deleting = null
      }
    }
  },
  mounted() {
    this.loadProducts()
  }
}
</script>
