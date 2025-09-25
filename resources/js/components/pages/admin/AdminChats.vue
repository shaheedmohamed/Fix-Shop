<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">All Conversations</h3>
      <span class="badge bg-info">{{ chats.length }} conversations</span>
    </div>
    
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Participants</th>
                <th>Title</th>
                <th>Last Activity</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="chat in chats" :key="chat.id" style="cursor: pointer;" @click="viewChat(chat)">
                <td>
                  <div class="d-flex flex-wrap gap-1">
                    <span class="badge bg-primary">{{ chat.buyer?.name || 'Unknown Buyer' }}</span>
                    <span class="badge bg-success">{{ chat.seller?.name || 'Unknown Seller' }}</span>
                  </div>
                </td>
                <td>{{ chat.product?.name || 'General Chat' }}</td>
                <td>{{ formatDate(chat.updated_at) }}</td>
                <td>{{ formatDate(chat.created_at) }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" @click.stop="viewChat(chat)">
                    <i class="fa-solid fa-eye me-1"></i>View Chat
                  </button>
                </td>
              </tr>
              <tr v-if="!loading && chats.length === 0">
                <td colspan="5" class="text-center text-muted">No conversations found</td>
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
              Chat: {{ selectedChat?.buyer?.name }} & {{ selectedChat?.seller?.name }}
              <span v-if="selectedChat?.product" class="text-muted">- {{ selectedChat.product.name }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="loadingMessages" class="text-center py-3">
              <div class="spinner-border" role="status"></div>
            </div>
            <div v-else-if="messages.length === 0" class="text-center text-muted py-3">
              No messages in this conversation
            </div>
            <div v-else class="chat-messages" style="max-height: 400px; overflow-y: auto;">
              <div v-for="message in messages" :key="message.id" class="message mb-3">
                <div class="d-flex align-items-start gap-2">
                  <div class="flex-shrink-0">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 32px; height: 32px; font-size: 0.8rem;">
                      {{ message.sender?.name?.charAt(0) || '?' }}
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <strong class="text-primary">{{ message.sender?.name || 'Unknown' }}</strong>
                      <small class="text-muted">{{ formatDate(message.created_at) }}</small>
                    </div>
                    <div class="bg-light p-2 rounded">{{ message.body || message.content }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AdminChatsPage',
  data() {
    return {
      chats: [],
      messages: [],
      selectedChat: null,
      loading: false,
      loadingMessages: false
    }
  },
  methods: {
    async loadChats() {
      this.loading = true
      try {
        const { data } = await axios.get('/api/admin/chats')
        this.chats = data?.data || data || []
      } catch (e) {
        console.error('Failed to load chats:', e)
      } finally {
        this.loading = false
      }
    },
    async viewChat(chat) {
      this.selectedChat = chat
      this.loadingMessages = true
      this.messages = []
      
      try {
        const { data } = await axios.get(`/api/chat/conversations/${chat.id}/messages`)
        this.messages = data || []
      } catch (e) {
        console.error('Failed to load messages:', e)
        alert('Failed to load chat messages')
      } finally {
        this.loadingMessages = false
      }
      
      // Show modal
      const modal = new bootstrap.Modal(this.$refs.chatModal)
      modal.show()
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleString()
    }
  },
  mounted() {
    this.loadChats()
  }
}
</script>

<style scoped>
.message:last-child {
  margin-bottom: 0 !important;
}
</style>
