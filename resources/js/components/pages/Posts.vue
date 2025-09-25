<template>
  <div class="social-feed">
    <!-- Header -->
    <div class="feed-header">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <h1 class="text-center mb-4">
              <i class="fa-solid fa-users me-2 text-primary"></i>
              Community Posts
            </h1>
            <p class="text-center text-muted mb-4">Share your thoughts, connect with others, and discover what's happening in our community.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <!-- Create Post Card -->
          <div v-if="isAuthenticated" class="card post-card mb-4">
            <div class="card-body">
              <div class="d-flex gap-3">
                <div class="avatar">
                  <i class="fa-solid fa-user"></i>
                </div>
                <div class="flex-grow-1">
                  <button class="btn btn-light w-100 text-start post-input" @click="showCreateModal = true">
                    What's on your mind, {{ userName }}?
                  </button>
                </div>
              </div>
              <div class="post-actions mt-3 pt-3 border-top">
                <button class="btn btn-light flex-fill" @click="showCreateModal = true">
                  <i class="fa-solid fa-image me-2 text-success"></i>Photo
                </button>
                <button class="btn btn-light flex-fill" @click="showCreateModal = true">
                  <i class="fa-solid fa-video me-2 text-primary"></i>Video
                </button>
                <button class="btn btn-light flex-fill" @click="showCreateModal = true">
                  <i class="fa-solid fa-smile me-2 text-warning"></i>Feeling
                </button>
              </div>
            </div>
          </div>

          <!-- Posts Feed -->
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Loading posts...</p>
          </div>

          <div v-else-if="items.length === 0" class="text-center py-5">
            <i class="fa-regular fa-newspaper fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No posts yet</h5>
            <p class="text-muted">Be the first to share something with the community!</p>
          </div>

          <div v-else class="posts-container">
            <div v-for="post in items" :key="post.id" class="card post-card mb-4">
              <!-- Post Header -->
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div class="avatar me-3">
                    <i class="fa-solid fa-user"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold">{{ post.user?.name || 'Anonymous' }}</h6>
                    <small class="text-muted">
                      {{ formatTime(post.created_at) }}
                      <i class="fa-solid fa-globe ms-1" title="Public"></i>
                    </small>
                  </div>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                      <i class="fa-solid fa-ellipsis"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#"><i class="fa-solid fa-bookmark me-2"></i>Save post</a></li>
                      <li><a class="dropdown-item" href="#"><i class="fa-solid fa-flag me-2"></i>Report</a></li>
                    </ul>
                  </div>
                </div>

                <!-- Post Content -->
                <div class="post-content mb-3">
                  <p class="mb-2">{{ post.content }}</p>
                </div>

                <!-- Post Image -->
                <div v-if="post.image_path" class="post-image mb-3">
                  <img :src="imageUrl(post.image_path)" class="img-fluid rounded" @click="openImageModal(post.image_path)" style="cursor: pointer;">
                </div>

                <!-- Post Stats -->
                <div class="post-stats d-flex justify-content-between align-items-center py-2 border-top border-bottom">
                  <div class="d-flex align-items-center gap-2">
                    <div class="reactions">
                      <template v-if="totalReactions(post) > 0">
                        <span v-if="post.reactions.like>0" class="reaction-icon" title="Likes">👍</span>
                        <span v-if="post.reactions.love>0" class="reaction-icon" title="Loves">❤️</span>
                        <span v-if="post.reactions.haha>0" class="reaction-icon" title="Haha">😂</span>
                      </template>
                      <small class="text-muted ms-1">{{ totalReactions(post) }}</small>
                    </div>
                  </div>
                  <div class="d-flex gap-3">
                    <small class="text-muted">{{ post.comments_count || post.comments?.length || 0 }} comments</small>
                    <small class="text-muted">{{ post.shares_count || 0 }} shares</small>
                  </div>
                </div>

                <!-- Post Actions -->
                <div class="post-actions mt-2">
                  <div class="reaction-wrapper flex-fill" @mouseenter="post.showReactionPicker=true" @mouseleave="post.showReactionPicker=false">
                    <button class="btn btn-light w-100" @click="toggleLike(post)">
                      <i class="fa-solid fa-thumbs-up me-2" :class="{ 'text-primary': post.myReaction==='like' }"></i>
                      {{ post.myReaction ? reactionLabel(post.myReaction) : 'Like' }}
                    </button>
                    <div v-if="post.showReactionPicker" class="reaction-picker shadow-sm">
                      <button type="button" class="reaction-btn" @click="setReaction(post,'like')" title="Like">👍</button>
                      <button type="button" class="reaction-btn" @click="setReaction(post,'love')" title="Love">❤️</button>
                      <button type="button" class="reaction-btn" @click="setReaction(post,'haha')" title="Haha">😂</button>
                    </div>
                  </div>
                  <button class="btn btn-light flex-fill" @click="toggleComments(post)">
                    <i class="fa-solid fa-comment me-2"></i>
                    Comment
                  </button>
                  <button class="btn btn-light flex-fill" @click="sharePost(post)">
                    <i class="fa-solid fa-share me-2"></i>
                    Share
                  </button>
                </div>

                <!-- Comments Section -->
                <div v-if="post.showComments" class="comments-section mt-3 pt-3 border-top">
                  <div v-if="isAuthenticated" class="d-flex gap-2 mb-3">
                    <div class="avatar avatar-sm">
                      <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="input-group">
                        <input v-model="post.newComment" type="text" class="form-control rounded-pill" placeholder="Write a comment..." @keyup.enter="addComment(post)">
                        <button class="btn btn-primary rounded-pill ms-2" @click="addComment(post)">
                          <i class="fa-solid fa-paper-plane"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-else class="alert alert-light border small mb-3" role="alert">
                    To comment, please <RouterLink :to="{ name: 'login' }">login</RouterLink> or <RouterLink :to="{ name: 'register' }">create an account</RouterLink>.
                  </div>
                  
                  <div v-for="comment in post.comments" :key="comment.id" class="comment mb-2">
                    <div class="d-flex gap-2">
                      <div class="avatar avatar-sm">
                        <i class="fa-solid fa-user"></i>
                      </div>
                      <div class="comment-content">
                        <div class="comment-bubble">
                          <strong>{{ comment.user?.name || 'Anonymous' }}</strong>
                          <p class="mb-0">{{ comment.content }}</p>
                        </div>
                        <div class="comment-actions">
                          <small class="text-muted">{{ formatTime(comment.created_at) }}</small>
                          <button class="btn btn-sm btn-link p-0 ms-2">Like</button>
                          <button class="btn btn-sm btn-link p-0 ms-2">Reply</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Post Modal -->
    <div v-if="showCreateModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" @click.self="showCreateModal = false">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create Post</h5>
            <button type="button" class="btn-close" @click="showCreateModal = false"></button>
          </div>
          <form @submit.prevent="createPost">
            <div class="modal-body">
              <div class="d-flex gap-3 mb-3">
                <div class="avatar">
                  <i class="fa-solid fa-user"></i>
                </div>
                <div>
                  <h6 class="mb-0">{{ userName }}</h6>
                  <small class="text-muted">
                    <i class="fa-solid fa-globe me-1"></i>Public
                  </small>
                </div>
              </div>
              <textarea v-model="newPost.content" class="form-control border-0" rows="4" placeholder="What's on your mind?" required></textarea>
              <div v-if="newPost.imagePreview" class="mt-3">
                <img :src="newPost.imagePreview" class="img-fluid rounded">
                <button type="button" class="btn btn-sm btn-danger mt-2" @click="removeImage">
                  <i class="fa-solid fa-trash me-1"></i>Remove Image
                </button>
              </div>
            </div>
            <div class="modal-footer border-0">
              <div class="d-flex justify-content-between w-100">
                <div class="d-flex gap-2">
                  <input ref="imageInput" type="file" accept="image/*" class="d-none" @change="handleImageUpload">
                  <button type="button" class="btn btn-light" @click="$refs.imageInput.click()">
                    <i class="fa-solid fa-image text-success"></i>
                  </button>
                  <button type="button" class="btn btn-light">
                    <i class="fa-solid fa-smile text-warning"></i>
                  </button>
                  <button type="button" class="btn btn-light">
                    <i class="fa-solid fa-map-marker-alt text-danger"></i>
                  </button>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="!newPost.content.trim() || submitting">
                  {{ submitting ? 'Posting...' : 'Post' }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <div v-if="showImageModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.9);" @click.self="showImageModal = false">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
          <div class="modal-body p-0">
            <img :src="selectedImage" class="img-fluid w-100">
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" @click="showImageModal = false"></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import auth from '../../store/auth'

export default {
  name: 'PostsPublicPage',
  data() {
    return {
      items: [],
      loading: false,
      showCreateModal: false,
      showImageModal: false,
      selectedImage: '',
      submitting: false,
      newPost: {
        content: '',
        image: null,
        imagePreview: null
      }
    }
  },
  computed: {
    isAuthenticated() {
      return auth.isAuthenticated()
    },
    userName() {
      return auth.state.user?.name || 'User'
    }
  },
  methods: {
    imageUrl(p) {
      if (!p) return ''
      if (p.startsWith('http')) return p
      if (p.startsWith('/storage/')) return p
      if (p.startsWith('img/')) return `/storage/${p}`
      return `/storage/img/${p}`
    },

    formatTime(dateString) {
      const date = new Date(dateString)
      const now = new Date()
      const diffInSeconds = Math.floor((now - date) / 1000)
      
      if (diffInSeconds < 60) return 'Just now'
      if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m`
      if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h`
      if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d`
      
      return date.toLocaleDateString()
    },

    async load() {
      this.loading = true
      try {
        const { data } = await axios.get('/api/posts')
        this.items = (data?.data || data || []).map(post => ({
          ...post,
          liked: false,
          showComments: false,
          newComment: '',
          comments: post.comments || [],
          // reactions state (could later be loaded from API)
          reactions: post.reactions || { like: 0, love: 0, haha: 0 },
          myReaction: null,
          showReactionPicker: false,
          likes_count: Math.floor(Math.random() * 50),
          shares_count: Math.floor(Math.random() * 10)
        }))
      } catch (e) {
        console.error('Failed to load posts:', e)
      } finally {
        this.loading = false
      }
    },

    handleImageUpload(event) {
      const file = event.target.files[0]
      if (file) {
        if (file.size > 2 * 1024 * 1024) {
          alert('Image size should be less than 2MB')
          return
        }
        
        this.newPost.image = file
        const reader = new FileReader()
        reader.onload = (e) => {
          this.newPost.imagePreview = e.target.result
        }
        reader.readAsDataURL(file)
      }
    },

    removeImage() {
      this.newPost.image = null
      this.newPost.imagePreview = null
      this.$refs.imageInput.value = ''
    },

    async createPost() {
      if (!this.isAuthenticated) {
        alert('Please login to create a post.')
        this.$router.push({ name: 'login' })
        return
      }
      if (!this.newPost.content.trim()) return
      
      this.submitting = true
      try {
        const formData = new FormData()
        formData.append('content', this.newPost.content)
        if (this.newPost.image) {
          formData.append('image', this.newPost.image)
        }

        const { data } = await axios.post('/api/posts', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        // Add new post to the beginning of the list
        this.items.unshift({
          ...data,
          liked: false,
          showComments: false,
          newComment: '',
          comments: [],
          reactions: { like: 0, love: 0, haha: 0 },
          myReaction: null,
          showReactionPicker: false,
          likes_count: 0,
          comments_count: 0,
          shares_count: 0,
          user: { name: this.userName }
        })

        // Reset form
        this.newPost = {
          content: '',
          image: null,
          imagePreview: null
        }
        this.showCreateModal = false
        
      } catch (error) {
        console.error('Failed to create post:', error)
        alert('Failed to create post. Please try again.')
      } finally {
        this.submitting = false
      }
    },

    toggleLike(post) {
      // toggle simple like using reaction system
      if (post.myReaction === 'like') {
        this.setReaction(post, null)
      } else {
        this.setReaction(post, 'like')
      }
    },

    setReaction(post, type) {
      // remove previous reaction count
      const prev = post.myReaction
      if (prev && post.reactions[prev] !== undefined) {
        post.reactions[prev] = Math.max(0, (post.reactions[prev] || 0) - 1)
      }
      // set new reaction
      post.myReaction = type || null
      if (type && post.reactions[type] !== undefined) {
        post.reactions[type] = (post.reactions[type] || 0) + 1
      }
      post.showReactionPicker = false
    },

    totalReactions(post) {
      const r = post.reactions || { like: 0, love: 0, haha: 0 }
      return (r.like || 0) + (r.love || 0) + (r.haha || 0)
    },

    reactionLabel(r) {
      return r === 'love' ? 'Love' : r === 'haha' ? 'Haha' : 'Like'
    },

    async toggleComments(post) {
      post.showComments = !post.showComments
      if (post.showComments && (!post.comments || post.comments.length === 0)) {
        try {
          const { data } = await axios.get(`/api/posts/${post.id}/comments`)
          post.comments = data || []
        } catch (error) {
          console.error('Failed to load comments:', error)
          post.comments = []
        }
      }
    },

    async addComment(post) {
      if (!this.isAuthenticated) {
        alert('Please login to comment.')
        this.$router.push({ name: 'login' })
        return
      }
      if (!post.newComment.trim()) return
      
      try {
        const { data } = await axios.post(`/api/posts/${post.id}/comments`, {
          content: post.newComment
        })
        
        post.comments.push(data)
        post.comments_count = (post.comments_count || 0) + 1
        post.newComment = ''
      } catch (error) {
        console.error('Failed to add comment:', error)
        alert('Failed to add comment. Please try again.')
      }
    },

    sharePost(post) {
      if (navigator.share) {
        navigator.share({
          title: 'Check out this post',
          text: post.content,
          url: window.location.href
        })
      } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href)
        alert('Link copied to clipboard!')
      }
      post.shares_count++
    },

    openImageModal(imagePath) {
      this.selectedImage = this.imageUrl(imagePath)
      this.showImageModal = true
    }
  },

  mounted() {
    this.load()
  }
}
</script>
 
<style scoped>
.social-feed {
  background: #f8f9fa;
  min-height: 100vh;
}

.feed-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 3rem 0;
}

.post-card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}

.post-card:hover {
  box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
}

.avatar.avatar-sm {
  width: 32px;
  height: 32px;
  font-size: 0.875rem;
}

.post-input {
  background: #f1f3f4;
  border: none;
  border-radius: 25px;
  padding: 12px 16px;
  color: #65676b;
}

.post-input:hover {
  background: #e4e6ea;
}

.post-actions {
  display: flex;
  gap: 8px;
}

.post-actions .btn {
  border: none;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.post-actions .btn:hover {
  background: #e4e6ea;
}

.reaction-wrapper { position: relative; }
.reaction-picker {
  position: absolute;
  bottom: calc(100% + 6px);
  left: 6px;
  background: #fff;
  padding: 6px 8px;
  border-radius: 999px;
  display: flex;
  gap: 6px;
  z-index: 5;
}
.reaction-btn {
  border: none;
  background: transparent;
  font-size: 1.25rem;
  line-height: 1;
  transition: transform 0.1s ease;
}
.reaction-btn:hover { transform: translateY(-2px) scale(1.15); }

.post-image img {
  border-radius: 8px;
  max-height: 500px;
  width: 100%;
  object-fit: cover;
}

.reaction-icon {
  font-size: 1.1rem;
  margin-right: 2px;
}

.reactions {
  display: flex;
  align-items: center;
}

.comment-bubble {
  background: #f1f3f4;
  border-radius: 16px;
  padding: 8px 12px;
  display: inline-block;
  max-width: 100%;
}

.comment-content {
  flex-grow: 1;
}

.comment-actions {
  margin-top: 4px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.comment-actions .btn-link {
  font-size: 0.875rem;
  color: #65676b;
  text-decoration: none;
}

.comment-actions .btn-link:hover {
  color: #1877f2;
}

.modal-content {
  border-radius: 12px;
  border: none;
}

.modal-header {
  border-bottom: 1px solid #e5e5e5;
  padding: 1rem 1.5rem;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  padding: 1rem 1.5rem;
}

.form-control {
  border: none;
  resize: none;
  font-size: 1.5rem;
  line-height: 1.4;
}

.form-control:focus {
  box-shadow: none;
  border: none;
}

.form-control::placeholder {
  color: #65676b;
}

.rounded-pill {
  border-radius: 25px !important;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.post-card {
  animation: fadeIn 0.5s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .feed-header {
    padding: 2rem 0;
  }
  
  .post-actions {
    flex-direction: column;
    gap: 4px;
  }
  
  .post-actions .btn {
    justify-content: center;
  }
}
</style>
