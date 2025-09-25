<template>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div>
              <h5 class="mb-0">
                <i class="fa-solid fa-comments me-2"></i>
                Chat with {{ otherUser?.name || 'Seller' }}
              </h5>
              <small class="text-muted" v-if="conversation?.product">
                About: {{ conversation.product.name }}
              </small>
            </div>
            <RouterLink to="/products" class="btn btn-sm btn-outline-secondary">
              <i class="fa-solid fa-arrow-left me-1"></i>Back
            </RouterLink>
          </div>
          
          <div class="card-body p-0">
            <!-- Messages Area -->
            <div class="messages-area" style="height: 400px; overflow-y: auto; padding: 1rem;">
              <div v-if="loadingMessages" class="text-center py-3">
                <div class="spinner-border" role="status"></div>
              </div>
              
              <div v-else-if="messages.length === 0" class="text-center text-muted py-4">
                <i class="fa-regular fa-comments fa-3x mb-3"></i>
                <p>No messages yet. Start the conversation!</p>
              </div>
              
              <div v-else>
                <div v-for="message in messages" :key="message.id" class="message mb-3">
                  <div class="d-flex" :class="{ 'justify-content-end': isMyMessage(message) }">
                    <div class="message-bubble" :class="{ 'my-message': isMyMessage(message), 'other-message': !isMyMessage(message) }">
                      <div class="message-content">{{ message.body || message.content }}</div>
                      <div class="message-time">
                        <small class="text-muted">{{ formatTime(message.created_at) }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Message Input -->
          <div class="card-footer">
            <form @submit.prevent="sendMessage" class="d-flex gap-2">
              <input 
                v-model="newMessage" 
                type="text" 
                class="form-control" 
                placeholder="Type your message..." 
                :disabled="sending"
                maxlength="5000"
              />
              <button type="submit" class="btn btn-primary" :disabled="!newMessage.trim() || sending">
                <i v-if="sending" class="fa-solid fa-spinner fa-spin"></i>
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
import auth from '../../store/auth'

export default {
  name: 'ChatConversationPage',
  props: ['id'],
  data() {
    return {
      conversation: null,
      messages: [],
      newMessage: '',
      loadingMessages: false,
      sending: false,
      otherUser: null
    }
  },
  methods: {
    async loadConversation() {
      this.loadingMessages = true
      try {
        const { data } = await axios.get(`/api/chat/conversations/${this.id}/messages`)
        this.messages = data || []
        
        // Get conversation details to show other user info
        if (this.messages.length > 0) {
          // Find the other user (not current user)
          const currentUserId = auth.state.user?.id
          const firstMessage = this.messages[0]
          if (firstMessage.sender?.id !== currentUserId) {
            this.otherUser = firstMessage.sender
          } else {
            // Find another sender in messages
            const otherMessage = this.messages.find(m => m.sender?.id !== currentUserId)
            if (otherMessage) {
              this.otherUser = otherMessage.sender
            }
          }
        }
        
        this.scrollToBottom()
      } catch (e) {
        console.error('Failed to load messages:', e)
        alert('Failed to load conversation')
      } finally {
        this.loadingMessages = false
      }
    },
    
    async sendMessage() {
      if (!this.newMessage.trim()) return
      
      this.sending = true
      try {
        const { data } = await axios.post(`/api/chat/conversations/${this.id}/messages`, {
          content: this.newMessage.trim()
        })
        
        this.messages.push(data)
        this.newMessage = ''
        this.scrollToBottom()
      } catch (e) {
        console.error('Failed to send message:', e)
        alert('Failed to send message')
      } finally {
        this.sending = false
      }
    },
    
    isMyMessage(message) {
      return message.sender?.id === auth.state.user?.id
    },
    
    formatTime(dateString) {
      return new Date(dateString).toLocaleString()
    },
    
    scrollToBottom() {
      this.$nextTick(() => {
        const messagesArea = this.$el.querySelector('.messages-area')
        if (messagesArea) {
          messagesArea.scrollTop = messagesArea.scrollHeight
        }
      })
    }
  },
  
  async mounted() {
    await this.loadConversation()
  },
  
  watch: {
    id() {
      this.loadConversation()
    }
  }
}
</script>

<style scoped>
.messages-area {
  background: #f8f9fa;
}

.message-bubble {
  max-width: 70%;
  padding: 0.75rem;
  border-radius: 1rem;
  margin-bottom: 0.5rem;
}

.my-message {
  background: #007bff;
  color: white;
  border-bottom-right-radius: 0.25rem;
}

.other-message {
  background: white;
  border: 1px solid #dee2e6;
  border-bottom-left-radius: 0.25rem;
}

.message-content {
  margin-bottom: 0.25rem;
}

.message-time {
  font-size: 0.75rem;
  opacity: 0.7;
}

.my-message .message-time {
  text-align: right;
}
</style>
