import { reactive } from 'vue'
import axios from 'axios'

const state = reactive({
  token: null,
  user: null,
})

function setToken(token) {
  state.token = token
  if (token) {
    localStorage.setItem('sp_token', token)
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  } else {
    localStorage.removeItem('sp_token')
    delete axios.defaults.headers.common['Authorization']
  }
}

function setUser(user) {
  state.user = user
  if (user) localStorage.setItem('sp_user', JSON.stringify(user))
  else localStorage.removeItem('sp_user')
}

function loadFromLocalStorage() {
  const t = localStorage.getItem('sp_token')
  const u = localStorage.getItem('sp_user')
  if (t) {
    state.token = t
    axios.defaults.headers.common['Authorization'] = `Bearer ${t}`
  }
  if (u) state.user = JSON.parse(u)
}

function isAuthenticated() { return !!state.token }

async function fetchUser() {
  if (!state.token) return null
  try {
    const { data } = await axios.get('/api/user')
    setUser(data)
    return data
  } catch (_) { return null }
}

async function logout() {
  try { await axios.post('/api/logout') } catch (_) {}
  setToken(null)
  setUser(null)
}

export default { state, setToken, setUser, loadFromLocalStorage, isAuthenticated, fetchUser, logout }
 
// Add helper to patch user locally and persist
export function patchUser(patch){
  const next = { ...(state.user || {}), ...patch }
  setUser(next)
  return next
}
