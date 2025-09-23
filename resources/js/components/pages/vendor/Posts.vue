<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">My Posts</h3>
      <RouterLink class="btn btn-primary" :to="{ name: 'vendor-post-new' }"><i class="fa-solid fa-plus me-2"></i>New Post</RouterLink>
    </div>
    <div class="row g-3">
      <div v-for="p in posts" :key="p.id" class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-start gap-3">
              <img v-if="p.image_path" :src="imageUrl(p.image_path)" class="rounded" style="width: 120px; height: 90px; object-fit: cover;" />
              <div>
                <p class="mb-2">{{ p.content }}</p>
                <div class="text-muted small">{{ new Date(p.created_at).toLocaleString() }}</div>
                <div class="mt-2">
                  <button class="btn btn-outline-danger btn-sm" @click="remove(p)"><i class="fa-regular fa-trash-can me-1"></i>Delete</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="!loading && posts.length===0" class="text-muted">No posts yet.</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'VendorPostsPage',
  data(){ return { posts: [], loading: false } },
  methods:{
    imageUrl(p){ return p?.startsWith('http') ? p : `/storage/${p}` },
    async load(){
      this.loading = true
      try{ const { data } = await axios.get('/api/vendor/posts'); this.posts = data || [] }
      catch(e){ /* ignore */ }
      finally{ this.loading = false }
    },
    async remove(p){
      if(!confirm('Delete post?')) return
      try{ await axios.delete(`/api/vendor/posts/${p.id}`); this.posts = this.posts.filter(x=>x.id!==p.id) }
      catch(e){ alert('Failed to delete') }
    }
  },
  mounted(){ this.load() }
}
</script>
