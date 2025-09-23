<template>
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-3">Create Post</h4>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="form-label">What's on your mind?</label>
          <textarea v-model="form.content" rows="4" class="form-control" required placeholder="Share an update..."></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Image (optional)</label>
          <input type="file" accept="image/*" class="form-control" @change="onImage">
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-primary" type="submit" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Publish
          </button>
          <RouterLink class="btn btn-light" :to="{ name:'vendor-posts' }">Cancel</RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'VendorPostFormPage',
  data(){ return { form: { content:'', image:null }, loading:false } },
  methods:{
    onImage(e){ this.form.image = e.target.files?.[0] || null },
    async submit(){
      this.loading = true
      try{
        const fd = new FormData()
        fd.append('content', this.form.content)
        if(this.form.image) fd.append('image', this.form.image)
        await axios.post('/api/vendor/posts', fd, { headers: { 'Content-Type':'multipart/form-data' } })
        this.$router.push({ name:'vendor-posts' })
      }catch(e){ alert(e?.response?.data?.message || 'Failed to publish post') }
      finally{ this.loading = false }
    }
  }
}
</script>
