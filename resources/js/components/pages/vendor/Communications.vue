<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">Communications</h3>
      <span class="badge bg-primary">{{ conversations.length }} conversations</span>
    </div>
    
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Product</th>
                <th>Last Message</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="conv in conversations" :key="conv.id">
                <td>
                  <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                         style="width: 32px; height: 32px; font-size: 0.8rem;">
                      {{ conv.buyer?.name?.charAt(0) || '?' }}
                    </div>
                    <div>
                      <div class="fw-bold">{{ conv.buyer?.name || 'Unknown Customer' }}</div>
                      <small class="text-muted">{{ conv.buyer?.email || '' }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <div v-if="conv.product">
                    <div class="fw-bold">{{ conv.product.name }}</div>
                    <small class="text-muted">Product inquiry</small>
                  </div>
                  <div v-else class="text-muted">General inquiry</div>
                </td>
                <td>
                  <div v-if="conv.messages && conv.messages.length > 0">
                    <div class="text-truncate" style="max-width: 200px;">
                      {{ conv.messages[0].body || conv.messages[0].content || 'No content' }}
                    </div>
                    <small class="text-muted">{{ formatTime(conv.messages[0].created_at) }}</small>
                  </div>
                  <div v-else class="text-muted">No messages</div>
                </td>
                <td>
                  <span class="badge" :class="getStatusBadge(conv)">
                    {{ getStatusText(conv) }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="openChat(conv)">
                    <i class="fa-solid fa-reply me-1"></i>Reply
                  </button>
                </td>
              </tr>
              <tr v-if="!loading && conversations.length === 0">
                <td colspan="5" class="text-center text-muted py-4">
                  <i class="fa-regular fa-comments fa-2x mb-2"></i>
                  <div>No customer conversations yet</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div v-if="loading" class="text-center py-3">
          <div class="spinner-border" role="status"></div>
        </div>
      </div>
    </div>

    <!-- Chat Modal -->
    <div class="modal fade" id="chatModal" tabindex="-1" ref="chatModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Chat with {{ selectedConversation?.buyer?.name || 'Customer' }}
              <span v-if="selectedConversation?.product" class="text-muted">
                - {{ selectedConversation.product.name }}
              </span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <!-- Messages -->
            <div class="chat-messages mb-3" style="height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1rem;">
              <div v-if="loadingMessages" class="text-center py-3">
                <div class="spinner-border" role="status"></div>
              </div>
              <div v-else-if="chatMessages.length === 0" class="text-center text-muted py-3">
                No messages yet
              </div>
              <div v-else>
                <div v-for="message in chatMessages" :key="message.id" class="message mb-2">
                  <div class="d-flex" :class="{ 'justify-content-end': isMyMessage(message) }">
                    <div class="message-bubble" :class="{ 'my-message': isMyMessage(message), 'other-message': !isMyMessage(message) }">
                      <div class="message-content">{{ message.body || message.content || 'No message content' }}</div>
                      <div class="message-time">
                        <small class="text-muted">{{ formatTime(message.created_at) }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Reply Form -->
            <form @submit.prevent="sendReply" class="d-flex gap-2">
              <input 
                v-model="replyMessage" 
                type="text" 
                class="form-control" 
                placeholder="Type your reply..." 
                :disabled="sendingReply"
                maxlength="5000"
              />
              <button type="submit" class="btn btn-primary" :disabled="!replyMessage.trim() || sendingReply">
                <i v-if="sendingReply" class="fa-solid fa-spinner fa-spin"></i>
                <i v-else class="fa-solid fa-paper-plane"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import auth from '../../../store/auth'

export default {
  name: 'VendorCommunicationsPage',
  data() {
    return {
      conversations: [],
      chatMessages: [],
      selectedConversation: null,
      replyMessage: '',
      loading: false,
      loadingMessages: false,
      sendingReply: false
    }
  },
  methods: {
    async loadConversations() {
      this.loading = true
      try {
        console.log('Loading vendor communications...')
        const { data } = await axios.get('/api/vendor/communications')
        console.log('Communications API response:', data)
        this.conversations = data || []
        console.log('Conversations loaded:', this.conversations.length)
      } catch (e) {
        console.error('Failed to load conversations:', e)
        console.error('Error response:', e.response?.data)
        alert('Failed to load conversations: ' + (e.response?.data?.message || e.message))
      } finally {
        this.loading = false
      }
    },
    
    async openChat(conversation) {
      console.log('Opening chat for conversation:', conversation)
      this.selectedConversation = conversation
      this.loadingMessages = true
      this.chatMessages = []
      
      try {
        console.log('Fetching messages for conversation ID:', conversation.id)
        const { data } = await axios.get(`/api/chat/conversations/${conversation.id}/messages`)
        console.log('Messages received:', data)
        this.chatMessages = data || []
        this.scrollChatToBottom()
      } catch (e) {
        console.error('Failed to load messages:', e)
        console.error('Error response:', e.response?.data)
        alert('Failed to load chat messages: ' + (e.response?.data?.message || e.message))
      } finally {
        this.loadingMessages = false
      }
      
      // Show modal
      this.$nextTick(() => {
        if (typeof bootstrap !== 'undefined') {
          const modal = new bootstrap.Modal(this.$refs.chatModal)
          modal.show()
        } else {
          console.error('Bootstrap is not loaded')
          // Fallback: show modal manually
          this.$refs.chatModal.style.display = 'block'
          this.$refs.chatModal.classList.add('show')
        }
      })
    },
    
    async sendReply() {
      if (!this.replyMessage.trim() || !this.selectedConversation) return
      
      this.sendingReply = true
      try {
        const { data } = await axios.post(`/api/chat/conversations/${this.selectedConversation.id}/messages`, {
          content: this.replyMessage.trim()
        })
        
        this.chatMessages.push(data)
        this.replyMessage = ''
        this.scrollChatToBottom()
        
        // Update conversation in list
        const convIndex = this.conversations.findIndex(c => c.id === this.selectedConversation.id)
        if (convIndex !== -1) {
          this.conversations[convIndex].messages = [data]
        }
      } catch (e) {
        console.error('Failed to send reply:', e)
        alert('Failed to send reply')
      } finally {
        this.sendingReply = false
      }
    },
    
    isMyMessage(message) {
      return message.sender?.id === auth.state.user?.id
    },
    
    formatTime(dateString) {
      return new Date(dateString).toLocaleString()
    },
    
    getStatusBadge(conversation) {
      const now = new Date()
      const lastActivity = new Date(conversation.updated_at)
      const hoursDiff = (now - lastActivity) / (1000 * 60 * 60)
      
      if (hoursDiff < 1) return 'bg-success'
      if (hoursDiff < 24) return 'bg-warning'
      return 'bg-secondary'
    },
    
    getStatusText(conversation) {
      const now = new Date()
      const lastActivity = new Date(conversation.updated_at)
      const hoursDiff = (now - lastActivity) / (1000 * 60 * 60)
      
      if (hoursDiff < 1) return 'Active'
      if (hoursDiff < 24) return 'Recent'
      return 'Inactive'
    },
    
    scrollChatToBottom() {
      this.$nextTick(() => {
        const chatArea = this.$el.querySelector('.chat-messages')
        if (chatArea) {
          chatArea.scrollTop = chatArea.scrollHeight
        }
      })
    }
  },
  
  mounted() {
    this.loadConversations()
  }
}
</script>

<style scoped>
.message-bubble {
  max-width: 70%;
  padding: 0.5rem 0.75rem;
  border-radius: 1rem;
  margin-bottom: 0.25rem;
}

.my-message {
  background: #007bff;
  color: white;
  border-bottom-right-radius: 0.25rem;
}

.other-message {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-bottom-left-radius: 0.25rem;
}

.message-content {
  margin-bottom: 0.25rem;
}

.message-time {
  font-size: 0.7rem;
  opacity: 0.7;
}

.my-message .message-time {
  text-align: right;
}
</style>
