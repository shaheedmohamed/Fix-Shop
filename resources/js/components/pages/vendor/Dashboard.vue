<template>
  <div>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted">Products</div>
                <div class="h4 fw-bold">{{ stats.products }}</div>
              </div>
              <i class="fa-solid fa-box fa-2x text-primary"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted">Posts</div>
                <div class="h4 fw-bold">{{ stats.posts }}</div>
              </div>
              <i class="fa-regular fa-newspaper fa-2x text-success"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm mt-4">
      <div class="card-body d-flex align-items-center gap-2 flex-wrap">
        <RouterLink class="btn btn-primary" :to="{ name: 'vendor-product-new' }"><i class="fa-solid fa-plus me-2"></i>Add Product</RouterLink>
        <RouterLink class="btn btn-outline-primary" :to="{ name: 'vendor-products' }">Manage Products</RouterLink>
        <RouterLink class="btn btn-primary" :to="{ name: 'vendor-post-new' }"><i class="fa-solid fa-pen-to-square me-2"></i>New Post</RouterLink>
        <RouterLink class="btn btn-outline-primary" :to="{ name: 'vendor-posts' }">Manage Posts</RouterLink>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'VendorDashboardPage',
  data(){
    return { stats: { products: 0, posts: 0 } }
  },
  async mounted(){
    try{
      const [p, ps] = await Promise.all([
        axios.get('/api/vendor/products'),
        axios.get('/api/vendor/posts')
      ])
      this.stats.products = p.data?.length || 0
      this.stats.posts = ps.data?.length || 0
    }catch(e){ /* ignore */ }
  }
}
</script>
