<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">Edit Product</h3>
      <div class="d-flex gap-2">
        <RouterLink class="btn btn-secondary" :to="{ name: 'vendor-products' }">
          <i class="fa-solid fa-arrow-left me-2"></i>Back to Products
        </RouterLink>
        <button class="btn btn-danger" @click="deleteProduct" :disabled="deleting">
          <i class="fa-solid fa-trash me-2"></i>
          {{ deleting ? 'Deleting...' : 'Delete Product' }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border" role="status"></div>
      <p class="mt-2">Loading product...</p>
    </div>

    <div v-else-if="!product" class="alert alert-danger">
      Product not found
    </div>

    <form v-else @submit.prevent="save" class="card shadow-sm">
      <div class="card-body">
        <div class="row g-3">
          <!-- Product Name -->
          <div class="col-md-6">
            <label class="form-label">Product Name *</label>
            <input v-model="form.name" type="text" class="form-control" required maxlength="255" />
          </div>

          <!-- Price -->
          <div class="col-md-3">
            <label class="form-label">Price (EGP) *</label>
            <input v-model="form.price" type="number" step="0.01" min="0" class="form-control" required />
          </div>

          <!-- Discount Price -->
          <div class="col-md-3">
            <label class="form-label">Discount Price (EGP)</label>
            <input v-model="form.price_discount" type="number" step="0.01" min="0" class="form-control" />
          </div>

          <!-- Category -->
          <div class="col-md-6">
            <label class="form-label">Category</label>
            <select v-model="form.category_id" class="form-select">
              <option value="">Select Category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>

          <!-- Subcategory -->
          <div class="col-md-6">
            <label class="form-label">Subcategory</label>
            <select v-model="form.subcategory_id" class="form-select">
              <option value="">Select Subcategory</option>
              <option v-for="sub in filteredSubcategories" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
            </select>
          </div>

          <!-- Brand -->
          <div class="col-md-4">
            <label class="form-label">Brand</label>
            <input v-model="form.brand" type="text" class="form-control" maxlength="100" />
          </div>

          <!-- Color -->
          <div class="col-md-4">
            <label class="form-label">Color</label>
            <input v-model="form.color" type="text" class="form-control" maxlength="50" />
          </div>

          <!-- Size -->
          <div class="col-md-4">
            <label class="form-label">Size</label>
            <input v-model="form.size" type="text" class="form-control" maxlength="50" />
          </div>

          <!-- Stock Quantity -->
          <div class="col-md-4">
            <label class="form-label">Stock Quantity</label>
            <input v-model="form.stock_qty" type="number" min="0" class="form-control" />
          </div>

          <!-- Minimum Order -->
          <div class="col-md-4">
            <label class="form-label">Minimum Order</label>
            <input v-model="form.min_order_qty" type="number" min="1" class="form-control" />
          </div>

          <!-- Unit -->
          <div class="col-md-4">
            <label class="form-label">Unit</label>
            <input v-model="form.unit" type="text" class="form-control" maxlength="20" placeholder="e.g. kg, piece, meter" />
          </div>

          <!-- SKU -->
          <div class="col-md-6">
            <label class="form-label">SKU</label>
            <input v-model="form.sku" type="text" class="form-control" maxlength="100" />
          </div>

          <!-- Current Image -->
          <div class="col-md-6" v-if="product.image_path">
            <label class="form-label">Current Image</label>
            <div class="border rounded p-2">
              <img :src="imageUrl(product.image_path)" alt="Current" style="max-height: 100px; max-width: 100%;" />
            </div>
          </div>

          <!-- New Image -->
          <div class="col-12">
            <label class="form-label">{{ product.image_path ? 'Change Image' : 'Product Image' }}</label>
            <input @change="handleImageChange" type="file" accept="image/*" class="form-control" />
            <small class="text-muted">Max size: 4MB. Formats: JPG, PNG, GIF</small>
          </div>

          <!-- Description -->
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea v-model="form.description" class="form-control" rows="4" maxlength="5000"></textarea>
          </div>

          <!-- SEO Fields -->
          <div class="col-12">
            <h5 class="border-bottom pb-2 mb-3">SEO Settings (Optional)</h5>
          </div>

          <div class="col-md-6">
            <label class="form-label">SEO Title</label>
            <input v-model="form.seo_title" type="text" class="form-control" maxlength="255" />
          </div>

          <div class="col-md-6">
            <label class="form-label">SEO Keywords</label>
            <input v-model="form.seo_keywords" type="text" class="form-control" placeholder="keyword1, keyword2, keyword3" />
          </div>

          <div class="col-12">
            <label class="form-label">SEO Description</label>
            <textarea v-model="form.seo_description" class="form-control" rows="3" maxlength="500"></textarea>
          </div>
        </div>
      </div>

      <div class="card-footer d-flex justify-content-between">
        <RouterLink class="btn btn-secondary" :to="{ name: 'vendor-products' }">Cancel</RouterLink>
        <button type="submit" class="btn btn-primary" :disabled="saving">
          <i class="fa-solid fa-save me-2"></i>
          {{ saving ? 'Updating...' : 'Update Product' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'VendorProductEditPage',
  props: ['id'],
  data() {
    return {
      product: null,
      form: {
        name: '',
        description: '',
        price: '',
        price_discount: '',
        category_id: '',
        subcategory_id: '',
        brand: '',
        color: '',
        size: '',
        unit: '',
        sku: '',
        stock_qty: '',
        min_order_qty: '',
        seo_title: '',
        seo_description: '',
        seo_keywords: ''
      },
      categories: [],
      subcategories: [],
      imageFile: null,
      loading: false,
      saving: false,
      deleting: false
    }
  },
  computed: {
    filteredSubcategories() {
      if (!this.form.category_id) return []
      return this.subcategories.filter(s => s.category_id == this.form.category_id)
    }
  },
  methods: {
    imageUrl(path) {
      if (!path) return '/images/placeholder-product.svg'
      if (path.startsWith('http')) return path
      if (path.startsWith('/storage/')) return path
      if (path.startsWith('img/')) return `/storage/${path}`
      return `/storage/img/${path}`
    },

    async loadProduct() {
      this.loading = true
      try {
        const { data } = await axios.get(`/api/vendor/products/${this.id}`)
        this.product = data
        
        // Fill form with product data
        Object.keys(this.form).forEach(key => {
          this.form[key] = data[key] || ''
        })
      } catch (e) {
        console.error('Failed to load product:', e)
        alert('Failed to load product')
        this.$router.push({ name: 'vendor-products' })
      } finally {
        this.loading = false
      }
    },

    async loadCategories() {
      try {
        const { data } = await axios.get('/api/admin/catalog/categories')
        this.categories = data || []
      } catch (e) {
        console.error('Failed to load categories:', e)
      }
    },

    async loadSubcategories() {
      try {
        const { data } = await axios.get('/api/admin/catalog/subcategories')
        this.subcategories = data || []
      } catch (e) {
        console.error('Failed to load subcategories:', e)
      }
    },

    handleImageChange(e) {
      this.imageFile = e.target.files[0] || null
    },

    async save() {
      this.saving = true
      try {
        const formData = new FormData()
        
        // Add form fields
        Object.keys(this.form).forEach(key => {
          if (this.form[key] !== '') {
            formData.append(key, this.form[key])
          }
        })

        // Add image if selected
        if (this.imageFile) {
          formData.append('image', this.imageFile)
        }

        await axios.post(`/api/vendor/products/${this.id}`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        alert('Product updated successfully!')
        this.$router.push({ name: 'vendor-products' })
      } catch (e) {
        console.error('Failed to update product:', e)
        alert('Failed to update product: ' + (e.response?.data?.message || e.message))
      } finally {
        this.saving = false
      }
    },

    async deleteProduct() {
      if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return
      }

      this.deleting = true
      try {
        await axios.delete(`/api/vendor/products/${this.id}`)
        alert('Product deleted successfully!')
        this.$router.push({ name: 'vendor-products' })
      } catch (e) {
        console.error('Failed to delete product:', e)
        alert('Failed to delete product: ' + (e.response?.data?.message || e.message))
      } finally {
        this.deleting = false
      }
    }
  },

  async mounted() {
    await Promise.all([
      this.loadProduct(),
      this.loadCategories(),
      this.loadSubcategories()
    ])
  }
}
</script>

<style scoped>
.card {
  border: none;
}

.form-label {
  font-weight: 600;
  color: #374151;
}

.border-bottom {
  border-color: #e5e7eb !important;
}
</style>
