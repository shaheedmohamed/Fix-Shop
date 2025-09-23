<template>
  <section class="container py-5">
    <h1 class="h3 mb-3">Admin Users</h1>
    <p class="text-muted mb-3">Showing latest 50 users.</p>

    <div v-if="loading" class="alert alert-info py-2">Loading users…</div>
    <div v-else-if="error" class="alert alert-danger py-2">{{ error }}</div>

    <div v-else>
      <div v-if="users.length === 0" class="alert alert-secondary py-2">No users found.</div>
      <div v-else class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Age</th>
              <th>Stage</th>
              <th>Role</th>
              <th>Joined</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id">
              <td>{{ u.id }}</td>
              <td>{{ u.name }}</td>
              <td>{{ u.email }}</td>
              <td>{{ u.age ?? '-' }}</td>
              <td>{{ u.stage ?? '-' }}</td>
              <td><span :class="['badge', u.role==='admin' ? 'bg-danger' : 'bg-secondary']">{{ u.role }}</span></td>
              <td>{{ new Date(u.created_at).toLocaleString() }}</td>
              <td class="d-flex gap-2">
                <RouterLink class="btn btn-sm btn-outline-primary" :to="{ name:'admin-user-profile', params: { id: u.id } }">👁 View Profile</RouterLink>
                <RouterLink class="btn btn-sm btn-outline-secondary" :to="{ name:'admin-user-profile', params: { id: u.id }, query: { tab: 'history' } }">🕓 History</RouterLink>
                <button class="btn btn-sm btn-success" :disabled="u.id === meId" :title="u.id===meId ? 'You cannot message yourself' : 'Start chat'" @click="messageUser(u.id)">💬 Message</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios'
import messages from '../../../store/messages'
import { useRouter } from 'vue-router'
import { reactive, toRefs } from 'vue'
import auth from '../../../store/auth'

export default {
  name: 'AdminUsersPage',
  setup(){
    const router = useRouter()
    const state = reactive({ users: [], loading: true, error: null })
    const meId = auth.state.user?.id || null
    const load = async () => {
      try {
        const { data } = await axios.get('/api/admin/users')
        state.users = data
      } catch (e) {
        state.error = e?.response?.data?.message || e.message || 'Failed to load users'
      } finally {
        state.loading = false
      }
    }
    const messageUser = async (userId) => {
      try {
        // navigate with user param; Messages page will start/open and focus input
        await router.push({ name: 'messages', query: { user: userId } })
      } catch (e) {
        console.error(e)
      }
    }
    load()
    return { ...toRefs(state), messageUser, meId }
  }
}
</script>
