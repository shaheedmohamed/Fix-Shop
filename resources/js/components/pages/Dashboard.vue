<template>
  <div class="py-5">
    <section class="container" v-reveal>
      <!-- Welcome Header -->
      <div class="text-center mb-5">
        <h1 class="fw-bold text-primary">Welcome Back, {{ userName }}!</h1>
        <p class="text-muted fs-5">Here's your shopping overview and account summary.</p>
      </div>

      <!-- Stats Cards -->
      <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100 card-hover bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4 d-flex align-items-center text-white">
              <div class="display-6 me-3 icon-pulse"><i class="fa-solid fa-shopping-bag"></i></div>
              <div>
                <div class="fw-bold fs-4">{{ stats.totalOrders }}</div>
                <div class="opacity-75">Total Orders</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100 card-hover bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body p-4 d-flex align-items-center text-white">
              <div class="display-6 me-3"><i class="fa-solid fa-heart"></i></div>
              <div>
                <div class="fw-bold fs-4">{{ stats.wishlistItems }}</div>
                <div class="opacity-75">Wishlist Items</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100 card-hover bg-gradient" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body p-4 d-flex align-items-center text-white">
              <div class="display-6 me-3"><i class="fa-solid fa-comments"></i></div>
              <div>
                <div class="fw-bold fs-4">{{ stats.activeChats }}</div>
                <div class="opacity-75">Active Chats</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100 card-hover bg-gradient" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="card-body p-4 d-flex align-items-center text-white">
              <div class="display-6 me-3"><i class="fa-solid fa-coins"></i></div>
              <div>
                <div class="fw-bold fs-4">EGP {{ stats.totalSpent }}</div>
                <div class="opacity-75">Total Spent</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 p-4">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Recent Orders</h5>
                <RouterLink to="/orders" class="btn btn-sm btn-outline-primary">View All</RouterLink>
              </div>
            </div>
            <div class="card-body p-0">
              <div v-if="loadingOrders" class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
              </div>
              <div v-else-if="recentOrders.length === 0" class="text-center py-5">
                <i class="fa-regular fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">No orders yet</p>
                <RouterLink to="/products" class="btn btn-primary">Start Shopping</RouterLink>
              </div>
              <div v-else class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Order #</th>
                      <th>Items</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in recentOrders" :key="order.id">
                      <td class="fw-bold">#{{ order.id }}</td>
                      <td>{{ order.items_count }} items</td>
                      <td class="fw-bold text-success">EGP {{ order.total }}</td>
                      <td>
                        <span class="badge" :class="getStatusBadge(order.status)">
                          {{ order.status }}
                        </span>
                      </td>
                      <td class="text-muted">{{ formatDate(order.created_at) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="col-lg-4">
          <!-- Quick Actions -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4">
              <h5 class="fw-bold mb-0"><i class="fa-solid fa-bolt me-2 text-warning"></i>Quick Actions</h5>
            </div>
            <div class="card-body p-4">
              <div class="d-grid gap-2">
                <RouterLink to="/products" class="btn btn-primary">
                  <i class="fa-solid fa-shopping-bag me-2"></i>Browse Products
                </RouterLink>
                <button class="btn btn-success" @click="showServiceRequestModal = true">
                  <i class="fa-solid fa-tools me-2"></i>Request Service
                </button>
                <button class="btn btn-outline-primary" @click="openCart">
                  <i class="fa-solid fa-cart-shopping me-2"></i>View Cart ({{ cartCount }})
                </button>
                <RouterLink to="/orders" class="btn btn-outline-secondary">
                  <i class="fa-solid fa-list me-2"></i>My Orders
                </RouterLink>
                <RouterLink to="/profile" class="btn btn-outline-secondary">
                  <i class="fa-solid fa-user me-2"></i>My Profile
                </RouterLink>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 p-4">
              <h5 class="fw-bold mb-0"><i class="fa-solid fa-bell me-2 text-info"></i>Recent Activity</h5>
            </div>
            <div class="card-body p-4">
              <div v-if="recentActivity.length === 0" class="text-center text-muted py-3">
                <i class="fa-regular fa-bell fa-2x mb-2"></i>
                <p class="mb-0">No recent activity</p>
              </div>
              <div v-else>
                <div v-for="activity in recentActivity" :key="activity.id" class="d-flex align-items-start mb-3">
                  <div class="flex-shrink-0 me-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                      <i :class="activity.icon" class="fa-solid fa-sm"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-1 fw-semibold">{{ activity.title }}</p>
                    <p class="mb-0 text-muted small">{{ activity.description }}</p>
                    <small class="text-muted">{{ formatTime(activity.created_at) }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recommended Products -->
      <div class="row mt-5">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 p-4">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-star me-2 text-warning"></i>Recommended for You</h5>
                <RouterLink to="/products" class="btn btn-sm btn-outline-primary">View All Products</RouterLink>
              </div>
            </div>
            <div class="card-body p-4">
              <div v-if="loadingRecommended" class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
              </div>
              <div v-else-if="recommendedProducts.length === 0" class="text-center py-4">
                <i class="fa-regular fa-lightbulb fa-3x text-muted mb-3"></i>
                <p class="text-muted">No recommendations available</p>
              </div>
              <div v-else class="row g-3">
                <div v-for="product in recommendedProducts" :key="product.id" class="col-md-6 col-lg-3">
                  <div class="card border-0 shadow-sm h-100 product-card">
                    <div class="position-relative">
                      <img :src="imageUrl(product.image_path)" class="card-img-top" style="height: 200px; object-fit: cover;" />
                      <div class="position-absolute top-0 end-0 p-2">
                        <button class="btn btn-sm btn-light rounded-circle" @click="toggleWishlist(product)">
                          <i class="fa-heart" :class="isInWishlist(product.id) ? 'fa-solid text-danger' : 'fa-regular'"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body p-3">
                      <h6 class="card-title fw-bold mb-2">{{ product.name }}</h6>
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-primary">EGP {{ product.price }}</div>
                        <RouterLink :to="{ name: 'product-detail', params: { id: product.id } }" class="btn btn-sm btn-primary">
                          View
                        </RouterLink>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

    <!-- Service Request Modal -->
    <div v-if="showServiceRequestModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" @click.self="showServiceRequestModal = false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fa-solid fa-tools me-2 text-primary"></i>
              Request a Service
            </h5>
            <button type="button" class="btn-close" @click="showServiceRequestModal = false"></button>
          </div>
          <form @submit.prevent="submitServiceRequest">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Service Category *</label>
                  <select v-model="serviceRequest.category" class="form-select" required>
                    <option value="">Select Category</option>
                    <option value="repair">Repair & Maintenance</option>
                    <option value="installation">Installation</option>
                    <option value="cleaning">Cleaning Services</option>
                    <option value="delivery">Delivery & Moving</option>
                    <option value="tutoring">Tutoring & Teaching</option>
                    <option value="design">Design & Creative</option>
                    <option value="tech">Tech Support</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Budget Range (EGP) *</label>
                  <select v-model="serviceRequest.budget" class="form-select" required>
                    <option value="">Select Budget</option>
                    <option value="0-100">0 - 100 EGP</option>
                    <option value="100-500">100 - 500 EGP</option>
                    <option value="500-1000">500 - 1,000 EGP</option>
                    <option value="1000-5000">1,000 - 5,000 EGP</option>
                    <option value="5000+">5,000+ EGP</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Service Title *</label>
                  <input v-model="serviceRequest.title" type="text" class="form-control" placeholder="e.g. Fix my laptop screen" required maxlength="100">
                </div>
                <div class="col-12">
                  <label class="form-label">Service Description *</label>
                  <textarea v-model="serviceRequest.description" class="form-control" rows="4" placeholder="Describe what you need in detail..." required maxlength="1000"></textarea>
                  <small class="text-muted">{{ serviceRequest.description.length }}/1000 characters</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Location</label>
                  <input v-model="serviceRequest.location" type="text" class="form-control" placeholder="City, Area">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Deadline</label>
                  <input v-model="serviceRequest.deadline" type="date" class="form-control" :min="new Date().toISOString().split('T')[0]">
                </div>
                <div class="col-12">
                  <label class="form-label">Attachments (Optional)</label>
                  <input @change="handleFileUpload" type="file" class="form-control" accept="image/*" multiple>
                  <small class="text-muted">Upload images to help explain your request (Max 5 files, 2MB each)</small>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showServiceRequestModal = false">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="submittingRequest">
                <i class="fa-solid fa-paper-plane me-2"></i>
                {{ submittingRequest ? 'Submitting...' : 'Submit Request' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script>
import { computed, ref, onMounted } from 'vue'
import axios from 'axios'
import Footer from '../layouts/Footer.vue'
import auth from '../../store/auth'
import cart from '../../store/cart'

export default {
  name: 'DashboardPage',
  components: { Footer },
  setup() {
    const stats = ref({
      totalOrders: 0,
      wishlistItems: 0,
      activeChats: 0,
      totalSpent: 0
    })
    
    const recentOrders = ref([])
    const loadingRecommended = ref(false)
    const recommendedProducts = ref([])
    const wishlist = ref(new Set())
    
    // Service Request Modal
    const showServiceRequestModal = ref(false)
    const submittingRequest = ref(false)
    const serviceRequest = ref({
      category: '',
      budget: '',
      title: '',
      description: '',
      location: '',
      deadline: '',
      attachments: []
    })
    
    const loadingOrders = ref(false)
    
    const userName = computed(() => auth.state.user?.name || 'User')
    const cartCount = computed(() => cart.count())
    // Mock data for demonstration
    const loadDashboardData = async () => {
      try {
        // Load recommended products
        loadingRecommended.value = true
        const { data } = await axios.get('/api/products?limit=4')
        recommendedProducts.value = data?.data || data || []
        
        // Mock stats (in real app, these would come from API)
        stats.value = {
          totalOrders: 12,
          wishlistItems: 5,
          activeChats: 3,
          totalSpent: 2450
        }
        
        // Mock recent orders
        recentOrders.value = [
          { id: 1001, items_count: 3, total: 450, status: 'delivered', created_at: '2025-09-20' },
          { id: 1002, items_count: 1, total: 120, status: 'shipped', created_at: '2025-09-22' },
          { id: 1003, items_count: 2, total: 280, status: 'processing', created_at: '2025-09-24' }
        ]
        
        // Mock recent activity
        recentActivity.value = [
          { 
            id: 1, 
            icon: 'fa-shopping-bag', 
            title: 'Order Delivered', 
            description: 'Your order #1001 has been delivered successfully',
            created_at: '2025-09-24T10:30:00Z'
          },
          { 
            id: 2, 
            icon: 'fa-heart', 
            title: 'Item Added to Wishlist', 
            description: 'You added "Wireless Headphones" to your wishlist',
            created_at: '2025-09-23T15:20:00Z'
          },
          { 
            id: 3, 
            icon: 'fa-comments', 
            title: 'New Message', 
            description: 'You have a new message from seller about your inquiry',
            created_at: '2025-09-23T09:15:00Z'
          }
        ]
        
      } catch (e) {
        console.error('Failed to load dashboard data:', e)
      } finally {
        loadingRecommended.value = false
      }
    }
    
    const imageUrl = (path) => {
      if (!path) return '/images/placeholder-product.svg'
      if (path.startsWith('http')) return path
      if (path.startsWith('/storage/')) return path
      // Fix for Laravel storage path
      if (path.startsWith('img/')) return `/storage/${path}`
      return `/storage/img/${path}`
    }
    
    const openCart = () => {
      cart.toggleSidebar()
    }
    
    const getStatusBadge = (status) => {
      const badges = {
        'pending': 'bg-warning',
        'processing': 'bg-info',
        'shipped': 'bg-primary',
        'delivered': 'bg-success',
        'cancelled': 'bg-danger'
      }
      return badges[status] || 'bg-secondary'
    }
    
    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString()
    }
    
    const formatTime = (dateString) => {
      return new Date(dateString).toLocaleString()
    }
    
    const isInWishlist = (productId) => {
      return wishlist.value.has(productId)
    }
    
    const toggleWishlist = (product) => {
      if (wishlist.value.has(product.id)) {
        wishlist.value.delete(product.id)
        stats.value.wishlistItems--
      } else {
        wishlist.value.add(product.id)
        stats.value.wishlistItems++
      }
    }
    
    const handleFileUpload = (event) => {
      const files = Array.from(event.target.files)
      if (files.length > 5) {
        alert('Maximum 5 files allowed')
        return
      }
      serviceRequest.value.attachments = files
    }
    
    const submitServiceRequest = async () => {
      submittingRequest.value = true
      try {
        const formData = new FormData()
        formData.append('category', serviceRequest.value.category)
        formData.append('budget', serviceRequest.value.budget)
        formData.append('title', serviceRequest.value.title)
        formData.append('description', serviceRequest.value.description)
        formData.append('location', serviceRequest.value.location)
        formData.append('deadline', serviceRequest.value.deadline)
        
        serviceRequest.value.attachments.forEach((file, index) => {
          formData.append(`attachments[${index}]`, file)
        })
        
        await axios.post('/api/service-requests', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        
        alert('Service request submitted successfully! Providers will be able to send you offers.')
        showServiceRequestModal.value = false
        
        // Reset form
        serviceRequest.value = {
          category: '',
          budget: '',
          title: '',
          description: '',
          location: '',
          deadline: '',
          attachments: []
        }
      } catch (error) {
        console.error('Error submitting service request:', error)
        alert('Failed to submit service request. Please try again.')
      } finally {
        submittingRequest.value = false
      }
    }
    
    onMounted(() => {
      loadDashboardData()
    })
    return {
      stats,
      recentOrders,
      recentActivity,
      recommendedProducts,
      loadingOrders,
      loadingRecommended,
      userName,
      cartCount,
      imageUrl,
      openCart,
      getStatusBadge,
      formatDate,
      toggleWishlist,
      isInWishlist,
      showServiceRequestModal,
      serviceRequest,
      submittingRequest,
      handleFileUpload,
      submitServiceRequest
    }
  }
}
</script>

<style scoped>
.card-hover {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card-hover:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.icon-pulse {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

.product-card {
  transition: transform 0.2s ease-in-out;
}

.product-card:hover {
  transform: translateY(-3px);
}

.bg-gradient {
  background-size: 200% 200%;
  animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>
