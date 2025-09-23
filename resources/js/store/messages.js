import { reactive } from 'vue'
import axios from 'axios'

const state = reactive({
  conversations: [], // {id,title,participants,last_message,unread}
  activeId: null,
  messages: [], // current conversation messages
  loading: false,
  convLoading: false,
  unreadCount: 0,
})

async function fetchConversations() {
  try {
    state.convLoading = true
    const { data } = await axios.get('/api/messages/conversations')
    state.conversations = data || []
    // refresh unread sum
    state.unreadCount = (state.conversations || []).reduce((a, c) => a + (c.unread || 0), 0)
  } finally {
    state.convLoading = false
  }
}

async function openConversation(id) {
  if (!id) return
  state.activeId = id
  state.loading = true
  try {
    const { data } = await axios.get(`/api/messages/conversations/${id}`)
    state.messages = data?.messages || []
    // mark read
    try { await axios.post(`/api/messages/conversations/${id}/read`) } catch (_) {}
    await fetchConversations()
  } finally {
    state.loading = false
  }
}

async function send(content) {
  if (!state.activeId || !content?.trim()) return
  const msg = { role: 'user', content: content.trim(), created_at: new Date().toISOString() }
  state.messages.push(msg)
  try {
    const { data } = await axios.post(`/api/messages/conversations/${state.activeId}/send`, { content: content.trim() })
    // optimistic already pushed; ensure alignment
    if (data?.id) {
      // could replace last optimistic message with server one if needed
    }
    await fetchConversations()
  } catch (e) {
    // revert optimistic if failed
    state.messages.pop()
    throw e
  }
}

async function startConversation(userId, firstMessage = '') {
  const { data } = await axios.post('/api/messages/start', { user_id: userId, message: firstMessage || undefined })
  await fetchConversations()
  if (data?.id) await openConversation(data.id)
  return data?.id
}

async function startConversationWithAdmin(firstMessage = '') {
  const { data } = await axios.post('/api/messages/start-admin', { message: firstMessage || undefined })
  await fetchConversations()
  if (data?.id) await openConversation(data.id)
  return data?.id
}

async function refreshUnread() {
  try {
    const { data } = await axios.get('/api/messages/unread-count')
    state.unreadCount = data?.count ?? 0
  } catch (_) {}
}

export default {
  state,
  fetchConversations,
  openConversation,
  send,
  startConversation,
  startConversationWithAdmin,
  refreshUnread,
}
