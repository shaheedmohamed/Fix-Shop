<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">Service Providers</h3>
      <span class="badge bg-primary">{{ providers.length }} providers</span>
    </div>
    
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Store Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="provider in providers" :key="provider.id">
                <td>{{ provider.name }}</td>
                <td>{{ provider.store_name || 'N/A' }}</td>
                <td>{{ provider.email }}</td>
                <td>{{ provider.phone || 'N/A' }}</td>
                <td>{{ new Date(provider.created_at).toLocaleDateString() }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-info me-2" @click="viewProfile(provider)">
                    <i class="fa-solid fa-user me-1"></i>Profile
                  </button>
                  <button class="btn btn-sm btn-outline-primary" @click="viewProducts(provider)">
                    <i class="fa-solid fa-box me-1"></i>Products
                  </button>
                </td>
              </tr>
              <tr v-if="!loading && providers.length === 0">
                <td colspan="6" class="text-center text-muted">No providers found</td>
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
  name: 'AdminProvidersPage',
  data() {
    return {
      providers: [],
      loading: false
    }
  },
  methods: {
    async loadProviders() {
      this.loading = true
      try {
        const { data } = await axios.get('/api/admin/providers')
        this.providers = data?.data || data || []
      } catch (e) {
        console.error('Failed to load providers:', e)
      } finally {
        this.loading = false
      }
    },
    viewProfile(provider) {
      this.$router.push({ name: 'admin-user-profile', params: { id: provider.id } })
    },
    viewProducts(provider) {
      // Could filter products by this provider
      alert(`View products for ${provider.name} - Feature coming soon`)
    }
  },
  mounted() {
    this.loadProviders()
  }
}
</script>
