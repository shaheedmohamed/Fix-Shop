<template>
  <div class="container py-4">
    <h2 class="mb-3">Posts</h2>
    <div class="row g-3">
      <div v-for="p in items" :key="p.id" class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-start gap-3">
              <img v-if="p.image_path" :src="imageUrl(p.image_path)" class="rounded" style="width: 160px; height: 120px; object-fit: cover;" />
              <div>
                <p class="mb-2">{{ p.content }}</p>
                <div class="text-muted small">{{ new Date(p.created_at).toLocaleString() }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="!loading && items.length===0" class="text-muted">No posts yet.</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'PostsPublicPage',
  data(){ return { items: [], loading: false } },
  methods: {
    imageUrl(p){ return p?.startsWith('http') ? p : `/storage/${p}` },
    async load(){
      this.loading = true
      try{ const { data } = await axios.get('/api/posts'); this.items = data?.data || data || [] }
      catch(e){ /* ignore */ }
      finally{ this.loading = false }
    }
  },
  mounted(){ this.load() }
}
</script>
