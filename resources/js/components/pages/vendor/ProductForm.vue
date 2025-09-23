<template>
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-3">Add Product</h4>
      <form @submit.prevent="submit">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Product Name</label>
            <input v-model="form.name" type="text" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Brand</label>
            <input v-model="form.brand" type="text" class="form-control" placeholder="Brand (optional)">
          </div>

          <div class="col-md-4">
            <label class="form-label">Category</label>
            <select class="form-select" v-model="form.category_id" @change="onCategoryChange">
              <option value="">Select Category</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Sub Category</label>
            <select class="form-select" v-model="form.subcategory_id">
              <option value="">Select Sub Category</option>
              <option v-for="s in filteredSubcategories" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">SKU</label>
            <div class="input-group">
              <input v-model="form.sku" type="text" class="form-control" placeholder="Ex: 134543">
              <button type="button" class="btn btn-outline-secondary" @click="genSku">Generate</button>
            </div>
          </div>

          <div class="col-md-3">
            <label class="form-label">Color</label>
            <input v-model="form.color" type="text" class="form-control" placeholder="Color">
          </div>
          <div class="col-md-3">
            <label class="form-label">Unit</label>
            <input v-model="form.unit" type="text" class="form-control" placeholder="Unit">
          </div>
          <div class="col-md-3">
            <label class="form-label">Size</label>
            <input v-model="form.size" type="text" class="form-control" placeholder="Size">
          </div>
          <div class="col-md-3">
            <label class="form-label">Stock Qty</label>
            <input v-model.number="form.stock_qty" type="number" min="0" step="1" class="form-control" placeholder="0">
          </div>

          <div class="col-md-3">
            <label class="form-label">Selling Price (EGP)</label>
            <input v-model.number="form.price" type="number" min="0" step="0.01" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Discount Price</label>
            <input v-model.number="form.price_discount" type="number" min="0" step="0.01" class="form-control" placeholder="0">
          </div>
          <div class="col-md-3">
            <label class="form-label">Min Order Qty</label>
            <input v-model.number="form.min_order_qty" type="number" min="1" step="1" class="form-control" placeholder="1">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea v-model="form.description" rows="3" class="form-control" placeholder="Describe your product"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Thumbnail (1:1)</label>
            <input type="file" accept="image/*" class="form-control" @change="onImage">
          </div>

          <div class="col-12">
            <hr/>
            <h6>SEO</h6>
          </div>
          <div class="col-md-4">
            <label class="form-label">SEO Product Name</label>
            <input v-model="form.seo_title" type="text" class="form-control" placeholder="SEO title">
          </div>
          <div class="col-md-8">
            <label class="form-label">SEO Description</label>
            <input v-model="form.seo_description" type="text" class="form-control" placeholder="Enter seo description">
          </div>
          <div class="col-12">
            <label class="form-label">Keywords</label>
            <input v-model="form.seo_keywords" type="text" class="form-control" placeholder="keyword1, keyword2">
          </div>
        </div>
        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary" type="submit" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Save
          </button>
          <RouterLink class="btn btn-light" :to="{ name:'vendor-products' }">Cancel</RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'VendorProductFormPage',
  data(){
    return { 
      form: { 
        name:'', brand:'', category_id:'', subcategory_id:'', sku:'', color:'', unit:'', size:'', stock_qty:0,
        price:0, price_discount:0, min_order_qty:1, description:'', image:null,
        seo_title:'', seo_description:'', seo_keywords:''
      }, 
      categories: [], subcategories: [],
      loading:false 
    }
  },
  methods:{
    onImage(e){ this.form.image = e.target.files?.[0] || null },
    async fetchCatalog(){
      const [cats, subs] = await Promise.all([
        axios.get('/api/catalog/categories'),
        axios.get('/api/catalog/subcategories'),
      ])
      this.categories = cats.data || []
      this.subcategories = subs.data || []
    },
    onCategoryChange(){
      if (!this.form.category_id) this.form.subcategory_id = ''
    },
    genSku(){ this.form.sku = 'SKU-' + Math.random().toString(36).slice(2,8).toUpperCase() },
    async submit(){
      this.loading = true
      try{
        const fd = new FormData()
        fd.append('name', this.form.name)
        if(this.form.brand) fd.append('brand', this.form.brand)
        if(this.form.category_id) fd.append('category_id', this.form.category_id)
        if(this.form.subcategory_id) fd.append('subcategory_id', this.form.subcategory_id)
        if(this.form.sku) fd.append('sku', this.form.sku)
        if(this.form.color) fd.append('color', this.form.color)
        if(this.form.unit) fd.append('unit', this.form.unit)
        if(this.form.size) fd.append('size', this.form.size)
        fd.append('stock_qty', this.form.stock_qty ?? 0)
        fd.append('price', this.form.price ?? 0)
        if(this.form.price_discount) fd.append('price_discount', this.form.price_discount)
        fd.append('min_order_qty', this.form.min_order_qty ?? 1)
        if(this.form.description) fd.append('description', this.form.description)
        if(this.form.image) fd.append('image', this.form.image)
        if(this.form.seo_title) fd.append('seo_title', this.form.seo_title)
        if(this.form.seo_description) fd.append('seo_description', this.form.seo_description)
        if(this.form.seo_keywords) fd.append('seo_keywords', this.form.seo_keywords)
        await axios.post('/api/vendor/products', fd, { headers: { 'Content-Type':'multipart/form-data' } })
        this.$router.push({ name:'vendor-products' })
      }catch(e){ alert(e?.response?.data?.message || 'Failed to save product') }
      finally{ this.loading = false }
    }
  },
  async mounted(){ await this.fetchCatalog() }
}
</script>
