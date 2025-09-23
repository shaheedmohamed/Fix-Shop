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
            <span class="badge bg-primary">lumix</span>
            <h1 class="h5 mb-0">Chat</h1>
          </div>
          <div class="d-flex align-items-center gap-2">
            <RouterLink :to="{ name: 'advisor' }" class="btn btn-outline-primary btn-sm">
              <i class="fa-solid fa-list-check me-1"></i> Create your plan
            </RouterLink>
            <div class="text-muted small">Model: {{ model }}</div>
          </div>
        </div>

        <div class="row g-0">
          <!-- Sidebar: conversations -->
          <aside class="col-12 col-lg-3 border-end" :class="{ 'd-none': !showSidebar && !isLg }">
            <div class="p-3 d-flex align-items-center justify-content-between">
              <strong class="small text-uppercase text-muted">History</strong>
              <button class="btn btn-sm btn-light" @click="newChat" :disabled="loading"><i class="fa-solid fa-plus"></i> New</button>
            </div>
            <div class="conv-list">
              <div v-if="convLoading" class="p-3 text-muted small">Loading…</div>
              <div v-else-if="conversations.length === 0" class="p-3 text-muted small">No conversations yet.</div>
              <button
                v-for="c in conversations"
                :key="c.id"
                class="conv-item"
                :class="{ active: c.id === activeConvId }"
                @click="openConversation(c.id)"
              >
                <i class="fa-regular fa-message me-2"></i>
                <span class="text-truncate">{{ c.title || 'Untitled' }}</span>
              </button>
            </div>
          </aside>

          <!-- Chat area -->
          <main class="col-12 col-lg-9">
            <div ref="scrollWrap" class="chat-wrap">
              <div class="p-3">
                <div v-for="(m, i) in messages" :key="i" class="mb-3">
                  <div :class="['msg', m.role]">
                    <div class="bubble">
                      <span v-if="m.role==='assistant'" class="me-2 text-primary"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                      <span v-html="format(m.content)"></span>
                    </div>
                  </div>
                </div>
                <div v-if="loading" class="mb-3">
                  <div class="msg assistant"><div class="bubble"><span class="typing"><span></span><span></span><span></span></span></div></div>
                </div>
              </div>
            </div>

            <form @submit.prevent="send" class="p-3 border-top bg-light-subtle">
              <div class="input-group">
                <input v-model="input" type="text" class="form-control" placeholder="Type your message…" :disabled="loading" required />
                <button class="btn btn-primary" :disabled="loading || !input.trim()">
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
import axios from 'axios'

export default {
  name: 'ChatPage',
  setup(){
    const messages = ref([
      { role: 'assistant', content: 'أهلاً! أنا <b>lumix</b> مساعدتك الذكية للدراسة. هساعدك تخططي/تخطط للدراسة بطريقتك وبأسلوب بسيط ومحترف. قولي/قلّي هدفك أو سؤالك وخلّينا نبدأ.' }
    ])
    const input = ref('')
    const loading = ref(false)
    const error = ref('')
    const model = ref('')
    const scrollWrap = ref(null)
    const conversations = ref([])
    const activeConvId = ref(null)
    const convLoading = ref(false)
    const showSidebar = ref(true)
    const isLg = computed(() => window.matchMedia('(min-width: 992px)').matches)

    const scrollToBottom = async () => {
      await nextTick()
      const el = scrollWrap.value
      if (el) el.scrollTop = el.scrollHeight
    }

    const format = (text) => {
      // naive markdown-ish line breaks
      return text.replace(/\n/g, '<br/>')
    }

    const send = async () => {
      if (!input.value.trim()) return
      error.value = ''
      const userMsg = { role: 'user', content: input.value.trim() }
      messages.value.push(userMsg)
      input.value = ''
      loading.value = true
      await scrollToBottom()
      try {
        const { data } = await axios.post('/api/ai/chat', {
          messages: messages.value.map(({ role, content }) => ({ role, content })),
          conversation_id: activeConvId.value || null
        })
        const reply = data.reply || ''
        model.value = data.model || model.value
        messages.value.push({ role: 'assistant', content: reply })
        if (!activeConvId.value && data.conversation_id) {
          activeConvId.value = data.conversation_id
          fetchConversations()
        }
      } catch (e) {
        error.value = e?.response?.data?.details || e?.response?.data?.message || 'Failed to get a reply.'
      } finally {
        loading.value = false
        await scrollToBottom()
      }
    }

    const fetchConversations = async () => {
      try {
        convLoading.value = true
        const { data } = await axios.get('/api/ai/conversations')
        conversations.value = data || []
      } catch (e) {
        // probably guest; ignore
      } finally { convLoading.value = false }
    }

    const openConversation = async (id) => {
      if (loading.value) return
      try {
        const { data } = await axios.get(`/api/ai/conversations/${id}`)
        activeConvId.value = id
        messages.value = data?.messages || []
        await scrollToBottom()
      } catch (e) {
        error.value = 'Failed to load conversation.'
      }
    }

    const newChat = () => {
      activeConvId.value = null
      messages.value = [ { role: 'assistant', content: 'بدأنا محادثة جديدة. أقدر أساعدك في إيه؟' } ]
      input.value = ''
      error.value = ''
    }

    onMounted(async () => {
      await scrollToBottom()
      fetchConversations()
    })

    return { messages, input, loading, error, model, scrollWrap, send, format, conversations, activeConvId, convLoading, openConversation, newChat, showSidebar, isLg }
  }
}
</script>

<style scoped>
.chat-wrap{ height: calc(100vh - 260px); overflow-y: auto; background: #f8fafc; }
.msg{ display: flex; }
.msg.user{ justify-content: flex-end; }
.msg .bubble{ max-width: min(680px, 90%); padding: .6rem .75rem; border-radius: .75rem; box-shadow: 0 2px 8px rgba(2,6,23,.06); }
.msg.user .bubble{ background: #0d6efd; color: #fff; border-bottom-right-radius: .25rem; }
.msg.assistant .bubble{ background: #ffffff; color: #0f172a; border-bottom-left-radius: .25rem; }

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
