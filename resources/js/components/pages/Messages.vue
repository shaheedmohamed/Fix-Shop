<template>
  <div class="min-vh-100 bg-light">
    <section class="container py-4 py-lg-5">
      <div class="bg-white rounded-3 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" @click="showSidebar = !showSidebar">
              <i class="fa-solid fa-bars"></i>
            </button>
            <h1 class="h5 mb-0">Messages</h1>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-success" @click="startAdmin"><i class="fa-regular fa-message me-1"></i> Message Admin</button>
            <span class="text-muted small">Unread: {{ store.state.unreadCount }}</span>
          </div>
        </div>

        <div class="row g-0">
          <!-- Sidebar: conversations -->
          <aside class="col-12 col-lg-3 border-end" :class="{ 'd-none': !showSidebar && !isLg }">
            <div class="p-3 d-flex align-items-center justify-content-between">
              <strong class="small text-uppercase text-muted">Conversations</strong>
              <button class="btn btn-sm btn-light" @click="refresh" :disabled="store.state.convLoading"><i class="fa-solid fa-rotate"></i></button>
            </div>
            <div class="conv-list">
              <div v-if="store.state.convLoading" class="p-3 text-muted small">Loading…</div>
              <div v-else-if="store.state.conversations.length === 0" class="p-3 text-muted small">No conversations yet.</div>
              <button
                v-for="c in store.state.conversations"
                :key="c.id"
                class="conv-item"
                :class="{ active: c.id === store.state.activeId }"
                @click="open(c.id)"
              >
                <i class="fa-regular fa-message me-2"></i>
                <span class="text-truncate flex-grow-1">{{ c.title || participantsOf(c) }}</span>
                <span v-if="(c.unread||0)>0" class="badge bg-danger ms-2">{{ c.unread }}</span>
              </button>
            </div>
          </aside>

          <!-- Chat area -->
          <main class="col-12 col-lg-9">
            <div ref="scrollWrap" class="chat-wrap">
              <div class="p-3">
                <div v-for="(m, i) in store.state.messages" :key="i" class="mb-3">
                  <div :class="['msg', m.role==='user' ? 'user' : 'other']">
                    <div class="bubble">
                      <span v-html="format(m.content)"></span>
                    </div>
                  </div>
                </div>
                <div v-if="store.state.loading" class="mb-3">
                  <div class="msg other"><div class="bubble"><span class="typing"><span></span><span></span><span></span></span></div></div>
                </div>
              </div>
            </div>

            <form @submit.prevent="handleSend" class="p-3 border-top bg-light-subtle">
              <div class="input-group">
                <input v-model="input" type="text" class="form-control" placeholder="Type a message…" :disabled="store.state.loading || !store.state.activeId" required />
                <button class="btn btn-primary" :disabled="store.state.loading || !input.trim() || !store.state.activeId">
                  <i class="fa-solid fa-paper-plane me-1"></i> Send
                </button>
              </div>
              <div v-if="error" class="text-danger small mt-2">{{ error }}</div>
            </form>
          </main>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, onMounted, nextTick, computed } from 'vue'
import store from '../../store/messages'
import auth from '../../store/auth'
import { useRoute, useRouter } from 'vue-router'

export default {
  name: 'MessagesPage',
  setup(){
    const input = ref('')
    const error = ref('')
    const scrollWrap = ref(null)
    const showSidebar = ref(true)
    const isLg = computed(() => window.matchMedia('(min-width: 992px)').matches)
    const route = useRoute()
    const router = useRouter()

    const scrollToBottom = async () => {
      await nextTick()
      const el = scrollWrap.value
      if (el) el.scrollTop = el.scrollHeight
    }

    const format = (text) => text.replace(/\n/g, '<br/>')

    const open = async (id) => {
      await store.openConversation(id)
      await scrollToBottom()
    }

    const refresh = async () => {
      await store.fetchConversations()
    }

    const handleSend = async () => {
      if (!input.value.trim()) return
      try {
        await store.send(input.value)
        input.value = ''
        await scrollToBottom()
      } catch (e) {
        error.value = e?.response?.data?.message || 'Failed to send'
      }
    }

    const startAdmin = async () => {
      try {
        await store.startConversationWithAdmin()
        await nextTick(); await scrollToBottom()
      } catch (e) {
        error.value = e?.response?.data?.message || 'Failed to start admin chat'
      }
    }

    onMounted(async () => {
      await store.fetchConversations()
      // If routed with ?conv=ID open it; or if ?user=ID start a new conversation
      const convId = route.query?.conv && Number(route.query.conv)
      const userId = route.query?.user && Number(route.query.user)
      if (convId) {
        await open(convId)
      } else if (userId) {
        await store.startConversation(userId)
      }
      // initial scroll
      await nextTick(); await scrollToBottom()
      // Poll unread
      setInterval(() => store.refreshUnread(), 10000)
    })

    const participantsOf = (c) => {
      const meId = auth.state.user?.id
      const names = (c?.participants || [])
        .filter(p => p.id !== meId)
        .map(p => p.name)
      return names.join(', ') || 'Conversation'
    }

    return { store, input, error, scrollWrap, format, handleSend, showSidebar, isLg, open, refresh, startAdmin, participantsOf }
  }
}
</script>

<style scoped>
.chat-wrap{ height: calc(100vh - 260px); overflow-y: auto; background: #f8fafc; }
.msg{ display: flex; }
.msg.user{ justify-content: flex-end; }
.msg .bubble{ max-width: min(680px, 90%); padding: .6rem .75rem; border-radius: .75rem; box-shadow: 0 2px 8px rgba(2,6,23,.06); }
.msg.user .bubble{ background: #0d6efd; color: #fff; border-bottom-right-radius: .25rem; }
.msg.other .bubble{ background: #ffffff; color: #0f172a; border-bottom-left-radius: .25rem; }

/* typing dots */
.typing{ display: inline-flex; align-items: center; gap: 4px; }
.typing span{ width: 6px; height: 6px; background: #94a3b8; border-radius: 50%; display: inline-block; animation: blink 1.2s infinite ease-in-out; }
.typing span:nth-child(2){ animation-delay: .2s; }
.typing span:nth-child(3){ animation-delay: .4s; }
@keyframes blink{ 0%, 80%, 100% { opacity: .2 } 40% { opacity: 1 } }

/* conversations */
.conv-list{ max-height: calc(100vh - 320px); overflow-y: auto; }
.conv-item{ width: 100%; text-align: left; background: transparent; border: 0; padding: .5rem .75rem; display: flex; align-items: center; font-size: .95rem; }
.conv-item:hover{ background: #f8fafc; }
.conv-item.active{ background: #eef2ff; color: #1d4ed8; }
</style>
